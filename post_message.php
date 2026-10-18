<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

require_once 'db_connection.php';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$messageText = isset($data['message_text']) ? trim($data['message_text']) : trim($_POST['message_text'] ?? '');

if ($messageText === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
    exit;
}

if (mb_strlen($messageText, 'UTF-8') > 1000) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Message exceeds maximum length of 1000 characters.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO messages (user_id, username, message_text) VALUES (?, ?, ?)');
    $stmt->execute([
        (int)$_SESSION['user_id'],
        $_SESSION['username'],
        $messageText
    ]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Message posted.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save message.']);
}
?>