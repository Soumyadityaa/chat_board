<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$currentUsername = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Board</title>
    <link rel="stylesheet" href="style.css">
</head>
<body data-user-id="<?php echo $currentUserId; ?>">
    <div class="chat-layout">
        <!-- Sticky Navigation Header -->
        <header class="chat-header">
            <div class="brand">
                <svg class="icon brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span class="brand-title">Message Board</span>
            </div>

            <div class="header-controls">
                <div class="user-badge" title="Logged in user">
                    <svg class="icon user-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span class="username-label"><?php echo $currentUsername; ?></span>
                </div>

                <button type="button" id="theme-toggle" class="icon-btn" aria-label="Toggle Theme" title="Toggle Dark/Light Mode">
                    <svg class="icon sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg class="icon moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>

                <a href="logout.php" class="icon-btn logout-btn" aria-label="Logout" title="Sign Out">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </header>

        <!-- Main Scrollable Message Feed -->
        <main id="message-feed" class="message-feed" aria-live="polite">
            <div class="feed-status">Loading messages...</div>
        </main>

        <!-- Bottom Message Input Area -->
        <footer class="chat-footer">
            <form id="message-form" class="message-form" autocomplete="off">
                <input
                    type="text"
                    id="message-input"
                    name="message_text"
                    class="message-input"
                    placeholder="Write a message..."
                    maxlength="1000"
                    required
                    aria-label="Message text"
                >
                <button type="submit" id="send-btn" class="send-btn" aria-label="Send Message">
                    <svg class="icon send-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span class="send-label">Send</span>
                </button>
            </form>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>