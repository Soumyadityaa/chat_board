document.addEventListener('DOMContentLoaded', () => {
    // 1. Theme Initialization and Persistence
    const themeToggleBtn = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('chat_board_theme') || 'light';

    document.documentElement.setAttribute('data-theme', savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('chat_board_theme', nextTheme);
        });
    }

    // 2. Chat Board Logic (Only runs on index.php)
    const messageFeed = document.getElementById('message-feed');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    if (!messageFeed || !messageForm) {
        return;
    }

    const currentUserId = parseInt(document.body.getAttribute('data-user-id'), 10);
    let lastRenderedMessageId = 0;
    let isInitialLoad = true;

    // Helper to check if user is scrolled near the bottom of the feed
    const isNearBottom = () => {
        const threshold = 100;
        return messageFeed.scrollHeight - messageFeed.scrollTop - messageFeed.clientHeight <= threshold;
    };

    const scrollToBottom = () => {
        messageFeed.scrollTop = messageFeed.scrollHeight;
    };

    // Create a DOM node for an individual message safely (prevents XSS)
    const createMessageElement = (msg) => {
        const isOwn = msg.user_id === currentUserId;

        const wrapper = document.createElement('div');
        wrapper.className = `message-item ${isOwn ? 'message-own' : 'message-other'}`;
        wrapper.dataset.id = msg.id;

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';

        const meta = document.createElement('div');
        meta.className = 'message-meta';

        const author = document.createElement('span');
        author.className = 'message-author';
        author.textContent = isOwn ? 'You' : msg.username;

        const time = document.createElement('time');
        time.className = 'message-time';
        time.textContent = msg.formatted_time;

        meta.appendChild(author);
        meta.appendChild(time);

        const text = document.createElement('div');
        text.className = 'message-text';
        text.textContent = msg.message_text;

        bubble.appendChild(meta);
        bubble.appendChild(text);
        wrapper.appendChild(bubble);

        return wrapper;
    };

    // Fetch messages from the server
    const fetchMessages = async (forceScroll = false) => {
        try {
            const response = await fetch('fetch_messages.php', {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                cache: 'no-store'
            });

            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }

            const data = await response.json();
            if (data.status !== 'success') {
                return;
            }

            const messages = data.messages || [];
            const shouldScroll = forceScroll || isInitialLoad || isNearBottom();

            if (messages.length === 0) {
                if (isInitialLoad) {
                    messageFeed.innerHTML = '<div class="feed-status">No messages yet. Start the conversation below.</div>';
                    isInitialLoad = false;
                }
                return;
            }

            const latestId = messages[messages.length - 1].id;
            if (latestId === lastRenderedMessageId && !isInitialLoad) {
                return; // No new messages to update
            }

            // Clear placeholder or rebuild feed when new messages arrive
            if (isInitialLoad || lastRenderedMessageId === 0) {
                messageFeed.innerHTML = '';
                messages.forEach((msg) => {
                    messageFeed.appendChild(createMessageElement(msg));
                });
            } else {
                messages.forEach((msg) => {
                    if (msg.id > lastRenderedMessageId) {
                        messageFeed.appendChild(createMessageElement(msg));
                    }
                });
            }

            lastRenderedMessageId = latestId;
            isInitialLoad = false;

            if (shouldScroll) {
                scrollToBottom();
            }
        } catch (error) {
            console.error('Error polling messages:', error);
        }
    };

    // Handle asynchronous message submission
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const text = messageInput.value.trim();
        if (!text) {
            return;
        }

        messageInput.value = '';
        messageInput.focus();
        sendBtn.disabled = true;

        try {
            const response = await fetch('post_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message_text: text })
            });

            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }

            const result = await response.json();
            if (result.status === 'success') {
                await fetchMessages(true);
            }
        } catch (error) {
            console.error('Failed to send message:', error);
        } finally {
            sendBtn.disabled = false;
        }
    });

    // Initial load & 2.5-second polling interval
    fetchMessages(true);
    setInterval(() => fetchMessages(false), 2500);
});