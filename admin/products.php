<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

$msg = '';
// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Get images to delete
    $res = $conn->query("SELECT main_image, image_1, image_2, image_3, image_4, image_5 FROM products WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        foreach ($row as $img) {
            if ($img && file_exists("../uploads/$img")) {
                unlink("../uploads/$img");
            }
        }
    }
    $conn->query("DELETE FROM products WHERE id = $id");
    $msg = '<div class="alert alert-success">Product deleted successfully!</div>';
}
// Fetch products with category name
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Products - Admin</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Manage Products</h2>
          <a href="add_product.php" class="btn btn-success">Add Product</a>
        </div>
        <?php echo $msg; ?>
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td>₹<?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                          <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                          <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">Delete</a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="7" class="text-center">No products found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html> 