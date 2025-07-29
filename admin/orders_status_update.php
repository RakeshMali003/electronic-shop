<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized');
}
require_once '../includes/db_connect.php';
$order_id = intval($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';
$allowed = ['pending', 'completed', 'cancelled'];
if ($order_id && in_array($status, $allowed)) {
    $stmt = $conn->prepare('UPDATE orders SET status=? WHERE id=?');
    $stmt->bind_param('si', $status, $order_id);
    if ($stmt->execute()) {
        echo 'Status updated';
    } else {
        http_response_code(500);
        echo 'Error updating status';
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo 'Invalid input';
} 