<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background: #f8f9fa; }
    .admin-navbar { background: #1a73e8; color: #fff; box-shadow: 0 2px 8px rgba(26,115,232,0.08); }
    .admin-navbar .navbar-brand { font-weight: 700; letter-spacing: 1px; color: #fff !important; display: flex; align-items: center; gap: 0.5rem; }
    .admin-navbar .navbar-brand img { width: 32px; height: 32px; }
    .sidebar { position: fixed; top: 56px; left: 0; height: 100vh; width: 220px; background: #fff; border-right: 1px solid #e3e6f0; padding-top: 2rem; z-index: 1030; box-shadow: 2px 0 8px rgba(26,115,232,0.04); }
    .sidebar .nav-link { color: #222; font-weight: 500; margin-bottom: 0.5rem; border-radius: 8px; transition: background 0.2s; padding: 0.75rem 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #e3f0ff; color: #1a73e8; }
    @media (max-width: 991.98px) { .sidebar { position: static; width: 100%; min-height: auto; border-right: none; border-bottom: 1px solid #e3e6f0; padding-top: 1rem; padding-bottom: 1rem; box-shadow: none; } }
    .main-content { margin-left: 220px; padding: 2rem 2rem 2rem 2rem; }
    @media (max-width: 991.98px) { .main-content { margin-left: 0; padding: 1rem; } }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container-fluid">
    <div class="row">
      <?php include 'sidebar.php'; ?>
      <main class="col-lg-10 col-md-9 ms-sm-auto px-4 py-5 main-content">
        <div class="card shadow p-4">
          <h2 class="mb-4 text-center">Orders Management</h2>
          <div id="orders-table-container" class="table-responsive"></div>
        </div>
      </main>
    </div>
  </div>
  <footer class="footer mt-5 p-4 text-center bg-white border-top">
    <div class="mb-2">
      <a href="#" class="me-3">Privacy Policy</a>
      <a href="#" class="me-3">Terms</a>
    </div>
    <div class="mt-2">
      &copy; <?php echo date('Y'); ?> Electronic Shop. All rights reserved.
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function loadOrders() {
      fetch('orders_data.php')
        .then(res => res.text())
        .then(html => {
          document.getElementById('orders-table-container').innerHTML = html;
          attachOrderEvents();
        });
    }
    function attachOrderEvents() {
      document.querySelectorAll('.order-status-select').forEach(function(select) {
        select.addEventListener('change', function() {
          const orderId = this.getAttribute('data-order-id');
          const newStatus = this.value;
          fetch('orders_status_update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'order_id=' + encodeURIComponent(orderId) + '&status=' + encodeURIComponent(newStatus)
          })
          .then(res => res.text())
          .then(msg => {
            loadOrders();
          });
        });
      });
      document.querySelectorAll('.view-order-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const orderId = this.getAttribute('data-order-id');
          alert('Order details for Order ID: ' + orderId + ' (feature coming soon)');
        });
      });
    }
    loadOrders();
    setInterval(loadOrders, 5000);
  </script>
</body>
</html> 