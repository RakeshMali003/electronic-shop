<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

// Add category
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $parent_id = $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;
    if ($name) {
        $stmt = $conn->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $parent_id);
        $stmt->execute();
    }
    header('Location: categories.php');
    exit;
}
// Edit category
if (isset($_POST['edit_category'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $parent_id = $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;
    if ($id && $name) {
        $stmt = $conn->prepare("UPDATE categories SET name=?, parent_id=? WHERE id=?");
        $stmt->bind_param('sii', $name, $parent_id, $id);
        $stmt->execute();
    }
    header('Location: categories.php');
    exit;
}
// Delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id=$id");
    header('Location: categories.php');
    exit;
}
// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$all_categories = $categories ? $categories->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories - Admin</title>
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
          <h2>Manage Categories</h2>
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="bi bi-plus-circle me-2"></i>Add Category</button>
        </div>
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($all_categories as $cat): ?>
                    <tr>
                      <td><?php echo $cat['id']; ?></td>
                      <td><?php echo htmlspecialchars($cat['name']); ?></td>
                      <td>
                        <?php
                        $parent = null;
                        foreach ($all_categories as $c) {
                          if ($c['id'] == $cat['parent_id']) $parent = $c['name'];
                        }
                        echo $parent ? htmlspecialchars($parent) : '-';
                        ?>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $cat['id']; ?>">Edit</button>
                        <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?');">Delete</a>
                      </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editCategoryModal<?php echo $cat['id']; ?>" tabindex="-1" aria-labelledby="editCategoryModalLabel<?php echo $cat['id']; ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="post">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editCategoryModalLabel<?php echo $cat['id']; ?>">Edit Category</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                              <div class="mb-3">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Parent Category</label>
                                <select name="parent_id" class="form-select">
                                  <option value="">None</option>
                                  <?php foreach ($all_categories as $c): if ($c['id'] != $cat['id']): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $cat['parent_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                  <?php endif; endforeach; ?>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" name="edit_category" class="btn btn-primary">Save Changes</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <!-- Add Modal -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Category Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Parent Category</label>
              <select name="parent_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($all_categories as $c): ?>
                  <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_category" class="btn btn-success">Add Category</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 