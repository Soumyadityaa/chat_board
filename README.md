# PHP & MySQL Real-Time Chat Board

A lightweight, responsive, and secure full-stack chat and message board application built with semantic HTML5, modern CSS3, Vanilla JavaScript (ES6+ Fetch API), PHP 8+, and MySQL. Designed to run seamlessly in a local XAMPP environment or on any standard LAMP/LEMP stack without external frameworks or dependencies.

---

## Key Features

- **User Authentication & Session Management:** Secure account registration and login flows powered by PHP Sessions, `password_hash()`, and `password_verify()`.
- **Asynchronous Real-Time Polling:** Uses the JavaScript ES6 `fetch` API to poll new messages every 2.5 seconds and submit posts asynchronously without full page reloads.
- **Smart Auto-Scroll:** Automatically scrolls to the newest message on initial load or when the user is near the bottom of the feed, without interrupting users scrolling through older history.
- **Dark & Light Theme Toggle:** Built with CSS custom properties (`:root` and `[data-theme="dark"]`) and persisted across sessions using `localStorage`.
- **Zero-Emoji SVG Interface:** Uses clean, scalable inline SVG icons for all visual indicators including navigation, user badges, theme toggles, and form controls.
- **Mobile-First Responsive Layout:** Constructed with CSS Grid and Flexbox (`100dvh` viewport sizing) for a sticky header, scrollable message feed, and fixed bottom message composer across mobile, tablet, and desktop screens.
- **Security-First Architecture:** Protects against SQL Injection via PDO prepared statements and prevents Cross-Site Scripting (XSS) via strict `textContent` DOM rendering and server-side output escaping.

---

## Tech Stack

- **Frontend:** HTML5, CSS3 (Grid, Flexbox, Custom Properties), Vanilla JavaScript (ES6+)
- **Backend:** PHP (PDO Extension, Sessions, JSON API endpoints)
- **Database:** MySQL / MariaDB (`utf8mb4` character set)
- **Local Server Environment:** XAMPP (Apache + MySQL)

---

## Project File Structure

```text
chat_board/
├── db_connection.php   # PDO database connection and error handling configuration
├── register.php        # New user registration form and validation logic
├── login.php           # User authentication and session initialization
├── logout.php          # Session destruction and redirect handler
├── index.php           # Protected main chat board interface
├── fetch_messages.php  # JSON GET endpoint returning the latest 100 messages
├── post_message.php    # JSON POST endpoint for validating and saving messages
├── script.js           # Client-side theme manager, AJAX polling, and DOM renderer
├── style.css           # Responsive layout, chat bubble styles, and theme variables
└── README.md           # Project documentation
```

---

## Local Installation & Setup (XAMPP)

### 1. Prerequisites
Download and install [XAMPP](https://www.apachefriends.org/) (or any PHP/MySQL environment running PHP 7.4+ or PHP 8.x).

### 2. Clone the Repository
Navigate to your XAMPP `htdocs` directory and clone this repository:

```bash
# Windows (Command Prompt / PowerShell)
cd C:\xampp\htdocs
git clone [https://github.com/your-username/php-mysql-chat-board.git](https://github.com/your-username/php-mysql-chat-board.git) chat_board

# macOS / Linux
cd /Applications/XAMPP/htdocs
git clone [https://github.com/your-username/php-mysql-chat-board.git](https://github.com/your-username/php-mysql-chat-board.git) chat_board
```

### 3. Start XAMPP Services
Open the **XAMPP Control Panel** and start both the **Apache** and **MySQL** modules.

### 4. Create the Database
1. Open your browser and navigate to `http://localhost/phpmyadmin`.
2. Click on the **SQL** tab at the top of the interface.
3. Copy and execute the following SQL schema:

```sql
CREATE DATABASE IF NOT EXISTS chat_board CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chat_board;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    message_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5. Verify Database Credentials
Open `db_connection.php` and confirm the credentials match your local MySQL setup (default XAMPP settings are pre-configured):

```php
$host = '127.0.0.1';
$db   = 'chat_board';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
```

### 6. Run the Application
Open your web browser and go to:
```text
http://localhost/chat_board/login.php
```
Register a new account, sign in, and start posting messages. To test real-time multi-user chatting locally, open a second browser window in Incognito/Private mode and log in with a second account.

---

## Security Overview

- **SQL Injection Prevention:** All database queries use PDO with `PDO::ATTR_EMULATE_PREPARES => false` and parameterized prepared statements.
- **Password Security:** User passwords are salted and hashed using PHP's `password_hash($password, PASSWORD_DEFAULT)`.
- **Session Hardening:** Calls `session_regenerate_id(true)` upon login to prevent session fixation attacks and properly invalidates session cookies on logout.
- **XSS Mitigation:** Server-side rendered strings are escaped using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`, and dynamic chat messages are injected strictly via `Element.textContent` rather than `innerHTML`.

---

## License

This project is open-source and available under the [MIT License](LICENSE).
