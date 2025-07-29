<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized');
}
require_once '../includes/db_connect.php';
$orders = $conn->query("SELECT o.id, o.user_id, o.status, o.created_at, u.name AS customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
echo '<table class="table table-bordered align-middle mb-0">';
echo '<thead class="table-light"><tr><th>Order ID</th><th>Customer</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>';
if ($orders && $orders->num_rows > 0) {
    while ($order = $orders->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($order['id']) . '</td>';
        echo '<td>' . htmlspecialchars($order['customer_name'] ?? 'Guest') . '</td>';
        echo '<td>';
        echo '<select class="form-select form-select-sm order-status-select" data-order-id="' . $order['id'] . '">';
        foreach (["pending", "completed", "cancelled"] as $status) {
            $selected = $order['status'] === $status ? 'selected' : '';
            echo '<option value="' . $status . '" ' . $selected . '>' . ucfirst($status) . '</option>';
        }
        echo '</select>';
        echo '</td>';
        echo '<td>' . date('d M Y, H:i', strtotime($order['created_at'])) . '</td>';
        echo '<td><button class="btn btn-sm btn-info view-order-btn" data-order-id="' . $order['id'] . '">View</button></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5" class="text-center text-muted">No orders found</td></tr>';
}
echo '</tbody></table>'; 