<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';
// Get stats
$products_count = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$categories_count = $conn->query("SELECT COUNT(*) FROM categories")->fetch_row()[0];
$orders_count = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0] ?? 0;
$users_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0;
// Low stock products (quantity < 5)
$low_stock = $conn->query("SELECT id, name, quantity FROM products WHERE quantity < 5 ORDER BY quantity ASC LIMIT 5");
$low_stock_products = $low_stock ? $low_stock->fetch_all(MYSQLI_ASSOC) : [];
// Fetch 5 most recent orders
$recent_orders = $conn->query("SELECT o.id, o.user_id, o.status, o.created_at, u.name AS customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders_list = $recent_orders ? $recent_orders->fetch_all(MYSQLI_ASSOC) : [];
// Sales summary counts
$sales_total = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$sales_completed = $conn->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetch_row()[0];
$sales_pending = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetch_row()[0];
// Recent admin activity (logins)
$recent_admin_logins = [];
if ($conn->query("SHOW TABLES LIKE 'admin_login_logs'")->num_rows > 0) {
  $logs = $conn->query("SELECT l.*, a.username FROM admin_login_logs l LEFT JOIN admins a ON l.admin_id = a.id ORDER BY l.id DESC LIMIT 5");
  if ($logs) $recent_admin_logins = $logs->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Electronic Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      background: #f8f9fa;
    }
    .admin-navbar {
      background: #1a73e8;
      color: #fff;
      box-shadow: 0 2px 8px rgba(26,115,232,0.08);
    }
    .admin-navbar .navbar-brand {
      font-weight: 700;
      letter-spacing: 1px;
      color: #fff !important;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .admin-navbar .navbar-brand img {
      width: 32px;
      height: 32px;
    }
    .sidebar {
      position: fixed;
      top: 56px;
      left: 0;
      height: 100vh;
      width: 220px;
      background: #fff;
      border-right: 1px solid #e3e6f0;
      padding-top: 2rem;
      z-index: 1030;
      box-shadow: 2px 0 8px rgba(26,115,232,0.04);
    }
    .sidebar .nav-link {
      color: #222;
      font-weight: 500;
      margin-bottom: 0.5rem;
      border-radius: 8px;
      transition: background 0.2s;
      padding: 0.75rem 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .sidebar .nav-link.active, .sidebar .nav-link:hover {
      background: #e3f0ff;
      color: #1a73e8;
    }
    .dashboard-widgets .card {
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(26,115,232,0.08);
      border: none;
    }
    .dashboard-widgets .card .icon {
      font-size: 2.2rem;
      color: #1a73e8;
      margin-bottom: 0.5rem;
    }
    @media (max-width: 991.98px) {
      .sidebar {
        position: static;
        width: 100%;
        min-height: auto;
        border-right: none;
        border-bottom: 1px solid #e3e6f0;
        padding-top: 1rem;
        padding-bottom: 1rem;
        box-shadow: none;
      }
    }
    .main-content {
      margin-left: 220px;
      padding: 2rem 2rem 2rem 2rem;
    }
    @media (max-width: 991.98px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar: Offcanvas for mobile, static for desktop -->
      <?php include 'sidebar.php'; ?>
      <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="adminSidebarLabel">Admin Menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body sidebar">
          <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
            <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
            <li class="nav-item"><a class="nav-link" href="orders.php"><i class="bi bi-receipt me-2"></i>Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
          </ul>
        </div>
      </div>
      <!-- Main Content -->
      <main class="col-lg-10 col-md-9 ms-sm-auto px-4 py-5 main-content">
        <h2 class="mb-4">Welcome, Admin!</h2>
        <div class="row dashboard-widgets g-4 mb-4">
          <div class="col-md-3">
            <div class="card text-center p-4">
              <div class="icon"><i class="bi bi-box-seam"></i></div>
              <h5 class="mb-1">Products</h5>
              <div class="display-6 fw-bold"><?php echo $products_count; ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center p-4">
              <div class="icon"><i class="bi bi-tags"></i></div>
              <h5 class="mb-1">Categories</h5>
              <div class="display-6 fw-bold"><?php echo $categories_count; ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center p-4">
              <div class="icon"><i class="bi bi-people"></i></div>
              <h5 class="mb-1">Users</h5>
              <div class="display-6 fw-bold"><?php echo $users_count; ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center p-4">
              <div class="icon"><i class="bi bi-receipt"></i></div>
              <h5 class="mb-1">Orders</h5>
              <div class="display-6 fw-bold"><?php echo $orders_count; ?></div>
            </div>
          </div>
        </div>
        <div class="row g-4 mb-4">
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white fw-bold">Low Stock Products</div>
              <div class="card-body p-0">
                <table class="table mb-0">
                  <thead><tr><th>Name</th><th>Quantity</th></tr></thead>
                  <tbody>
                  <?php if (empty($low_stock_products)): ?>
                    <tr><td colspan="2" class="text-center text-muted">No low stock products</td></tr>
                  <?php else: foreach ($low_stock_products as $p): ?>
                    <tr><td><?php echo htmlspecialchars($p['name']); ?></td><td><span class="badge bg-danger"><?php echo $p['quantity']; ?></span></td></tr>
                  <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white fw-bold">Recent Orders</div>
              <div class="card-body p-0">
                <table class="table mb-0">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Customer</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($recent_orders_list)): ?>
                    <tr><td colspan="4" class="text-center text-muted">No recent orders</td></tr>
                  <?php else: foreach ($recent_orders_list as $order): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($order['id']); ?></td>
                      <td><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></td>
                      <td><span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'secondary'); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                      <td><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></td>
                    </tr>
                  <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-4 mb-4">
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white fw-bold">Sales Summary</div>
              <div class="card-body text-center">
                <div class="row g-3 justify-content-center">
                  <div class="col-6 col-md-4">
                    <div class="p-3 border rounded bg-light">
                      <div class="fw-bold fs-4"><?php echo $sales_total; ?></div>
                      <div class="text-muted">Total Orders</div>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="p-3 border rounded bg-light">
                      <div class="fw-bold fs-4"><?php echo $sales_completed; ?></div>
                      <div class="text-success">Completed</div>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="p-3 border rounded bg-light">
                      <div class="fw-bold fs-4"><?php echo $sales_pending; ?></div>
                      <div class="text-warning">Pending</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white fw-bold">Recent Activity</div>
              <div class="card-body p-0">
                <table class="table mb-0">
                  <thead>
                    <tr>
                      <th>Admin</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($recent_admin_logins)): ?>
                    <tr><td colspan="3" class="text-center text-muted">No recent admin activity</td></tr>
                  <?php else: foreach ($recent_admin_logins as $log): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></td>
                      <td><span class="badge bg-<?php echo $log['status'] === 'success' ? 'success' : 'danger'; ?>"><?php echo ucfirst($log['status']); ?></span></td>
                      <td><?php echo date('d M Y, H:i', strtotime($log['created_at'] ?? $log['timestamp'] ?? '')); ?></td>
                    </tr>
                  <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="card shadow p-4">
          <h4 class="mb-3">Quick Actions</h4>
          <div class="d-flex flex-wrap gap-3">
            <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i>Add Product</a>
            <a href="products.php" class="btn btn-primary"><i class="bi bi-box-seam me-2"></i>Manage Products</a>
            <a href="#" class="btn btn-secondary"><i class="bi bi-tags me-2"></i>Manage Categories</a>
            <a href="#" class="btn btn-info text-white"><i class="bi bi-receipt me-2"></i>View Orders</a>
          </div>
        </div>
      </main>
    </div>
  </div>
  <footer class="footer mt-5 p-4 text-center bg-white border-top">
    <div class="mb-2">
      <a href="#" class="me-3">Privacy Policy</a>
      <a href="#" class="me-3">Terms</a>
      <a href="#" class="me-3"><img src="https://img.icons8.com/ios-filled/24/1a73e8/facebook-new.png" alt="Facebook"></a>
      <a href="#" class="me-3"><img src="https://img.icons8.com/ios-filled/24/1a73e8/instagram-new.png" alt="Instagram"></a>
      <a href="#"><img src="https://img.icons8.com/ios-filled/24/1a73e8/twitter.png" alt="Twitter"></a>
    </div>
    <div class="mt-2">
      &copy; <?php echo date('Y'); ?> Electronic Shop. All rights reserved.
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 