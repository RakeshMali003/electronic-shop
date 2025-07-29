<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';
$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    echo '<div class="container py-5"><div class="alert alert-danger">Invalid order ID.</div></div>';
    exit;
}
// Check if user is logged in
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_user = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
if (!$is_admin && !$is_user) {
    header('Location: user_login.php');
    exit;
}
// Fetch order
$order = $conn->prepare('SELECT o.*, u.name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?');
$order->bind_param('i', $order_id);
$order->execute();
$result = $order->get_result();
$order_data = $result ? $result->fetch_assoc() : null;
$order->close();
if (!$order_data) {
    echo '<div class="container py-5"><div class="alert alert-danger">Order not found.</div></div>';
    exit;
}
// Only allow owner or admin
if ($is_user && $_SESSION['user_id'] != $order_data['user_id']) {
    echo '<div class="container py-5"><div class="alert alert-danger">You are not allowed to view this order.</div></div>';
    exit;
}
// Fetch order items
$items = $conn->prepare('SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
$items->bind_param('i', $order_id);
$items->execute();
$items_result = $items->get_result();
$order_items = $items_result ? $items_result->fetch_all(MYSQLI_ASSOC) : [];
$items->close();
include 'includes/header.php';
?>
<div class="container py-5">
  <h2 class="mb-4">Order Details</h2>
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Order #<?php echo htmlspecialchars($order_data['id']); ?></h5>
      <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order_data['status'])); ?></p>
      <p><strong>Date:</strong> <?php echo htmlspecialchars($order_data['created_at']); ?></p>
      <p><strong>Customer:</strong> <?php echo htmlspecialchars($order_data['name'] ?? 'Guest'); ?> (<?php echo htmlspecialchars($order_data['email'] ?? ''); ?>)</p>
    </div>
  </div>
  <h4>Products</h4>
  <?php if (count($order_items) > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order_items as $item): ?>
            <tr>
              <td><?php echo htmlspecialchars($item['name']); ?></td>
              <td><?php echo htmlspecialchars($item['quantity']); ?></td>
              <td><?php echo htmlspecialchars($item['price']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No products found for this order.</div>
  <?php endif; ?>
  <a href="profile.php" class="btn btn-secondary mt-3">Back to My Orders</a>
</div>
<?php include 'includes/footer.php'; ?> 