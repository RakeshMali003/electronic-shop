<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: user_login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Fetch user orders
$orders = $conn->prepare('SELECT id, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->bind_param('i', $user_id);
$orders->execute();
$result = $orders->get_result();
$user_orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$orders->close();
?>
<?php include 'includes/header.php'; ?>
<div class="container py-5">
  <h2 class="mb-4">My Profile</h2>
  <!-- Existing profile content here -->
  <hr>
  <h3 class="mb-3">My Orders</h3>
  <?php if (count($user_orders) > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Order ID</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($user_orders as $order): ?>
            <tr>
              <td><?php echo htmlspecialchars($order['id']); ?></td>
              <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
              <td><?php echo htmlspecialchars($order['created_at']); ?></td>
              <td><a href="order_details.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm btn-primary">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">You have not placed any orders yet.</div>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?> 