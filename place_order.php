<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
function get_setting($key, $default = '') {
    global $conn;
    $stmt = $conn->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?');
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $stmt->bind_result($val);
    $stmt->fetch();
    $stmt->close();
    return $val !== null ? $val : $default;
}
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(403);
    exit('Login required');
}
$user_id = $_SESSION['user_id'];
$payment_id = $_POST['payment_id'] ?? '';
if (!$payment_id) {
    http_response_code(400);
    exit('Missing payment ID');
}
// For demo: get cart items and total (replace with real cart logic)
$items = get_cart_items();
$total = 0;
foreach ($items as $item) $total += $item['total'];
$tax_rate = floatval(get_setting('tax_rate', '18'));
$tax = round($total * $tax_rate / 100);
$grand_total = $total + $tax;
$currency = get_setting('currency', 'INR');
// Save order
$stmt = $conn->prepare('INSERT INTO orders (user_id, status, created_at, payment_id) VALUES (?, ?, NOW(), ?)');
$status = 'completed';
$stmt->bind_param('iss', $user_id, $status, $payment_id);
if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    // Send emails
    $user_email = $conn->query("SELECT email FROM users WHERE id=$user_id")->fetch_row()[0];
    $admin_email = get_setting('order_notification_email', get_setting('contact_email', 'info@electronicshop.com'));
    $subject = "Order Confirmation #$order_id";
    $body = "Thank you for your order!\nOrder ID: $order_id\nTotal: $currency$grand_total\nPayment ID: $payment_id\n";
    mail($user_email, $subject, $body);
    mail($admin_email, "New Order #$order_id", "A new order has been placed.\nOrder ID: $order_id\nUser ID: $user_id\nTotal: $currency$grand_total\nPayment ID: $payment_id\n");
    // Clear cart
    unset($_SESSION['cart']);
    echo "Order placed successfully! Confirmation sent to your email.";
} else {
    http_response_code(500);
    echo "Error saving order.";
} 