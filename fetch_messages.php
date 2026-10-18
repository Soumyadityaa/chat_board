<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once 'db_connection.php';

try {
    $stmt = $pdo->query('
        SELECT id, user_id, username, message_text, created_at
        FROM (
            SELECT id, user_id, username, message_text, created_at
            FROM messages
            ORDER BY id DESC
            LIMIT 100
        ) AS recent_messages
        ORDER BY id ASC
    ');

    $messages = [];
    while ($row = $stmt->fetch()) {
        $timestamp = strtotime($row['created_at']);
        $messages[] = [
            'id'             => (int)$row['id'],
            'user_id'        => (int)$row['user_id'],
            'username'       => $row['username'],
            'message_text'   => $row['message_text'],
            'formatted_time' => date('M j, g:i A', $timestamp)
        ];
    }

    echo json_encode([
        'status'   => 'success',
        'messages' => $messages
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch messages']);
}
?>