<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: products.php');
    exit;
}

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    header('Location: products.php');
    exit;
}

// Fetch categories (with parent info)
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$all_categories = $categories ? $categories->fetch_all(MYSQLI_ASSOC) : [];

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'active';
    // Handle image uploads
    $main_image = $product['main_image'];
    $images = [
        1 => $product['image_1'],
        2 => $product['image_2'],
        3 => $product['image_3'],
        4 => $product['image_4'],
        5 => $product['image_5'],
    ];
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
        $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $main_image = uniqid('main_') . '.' . $ext;
        move_uploaded_file($_FILES['main_image']['tmp_name'], '../uploads/' . $main_image);
    }
    for ($i = 1; $i <= 5; $i++) {
        if (isset($_FILES['image_' . $i]) && $_FILES['image_' . $i]['error'] === 0) {
            $ext = pathinfo($_FILES['image_' . $i]['name'], PATHINFO_EXTENSION);
            $images[$i] = uniqid('img' . $i . '_') . '.' . $ext;
            move_uploaded_file($_FILES['image_' . $i]['tmp_name'], '../uploads/' . $images[$i]);
        }
    }
    // Update DB
    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, price=?, quantity=?, description=?, status=?, main_image=?, image_1=?, image_2=?, image_3=?, image_4=?, image_5=? WHERE id=?");
    $stmt->bind_param('sidissssssssi', $name, $category_id, $price, $quantity, $description, $status, $main_image, $images[1], $images[2], $images[3], $images[4], $images[5], $id);
    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success">Product updated successfully!</div>';
        // Refresh product data
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
    } else {
        $msg = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
      <div class="ms-auto">
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
      </div>
    </div>
  </nav>
  <div class="container py-5">
    <div class="card shadow p-4">
      <h2 class="mb-4">Edit Product</h2>
      <?php echo $msg; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
              <option value="">Select Category</option>
              <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                  <?php
                  if ($cat['parent_id']) {
                    foreach ($all_categories as $p) {
                      if ($p['id'] == $cat['parent_id']) {
                        echo htmlspecialchars($p['name']) . ' > ';
                        break;
                      }
                    }
                  }
                  echo htmlspecialchars($cat['name']);
                  ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Price (₹)</label>
            <input type="number" name="price" class="form-control" required min="0" step="0.01" value="<?php echo $product['price']; ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required min="0" value="<?php echo $product['quantity']; ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active" <?php if ($product['status'] == 'active') echo 'selected'; ?>>Active</option>
              <option value="inactive" <?php if ($product['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Main Image</label>
            <input type="file" name="main_image" class="form-control" accept="image/*">
            <?php if ($product['main_image']): ?>
              <img src="../uploads/<?php echo $product['main_image']; ?>" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
          </div>
          <?php for ($i = 1; $i <= 5; $i++): ?>
          <div class="col-md-6">
            <label class="form-label">Additional Image <?php echo $i; ?></label>
            <input type="file" name="image_<?php echo $i; ?>" class="form-control" accept="image/*">
            <?php if ($product['image_' . $i]): ?>
              <img src="../uploads/<?php echo $product['image_' . $i]; ?>" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
          </div>
          <?php endfor; ?>
        </div>
        <button type="submit" class="btn btn-success mt-4">Update Product</button>
        <a href="products.php" class="btn btn-secondary mt-4 ms-2">Back to Products</a>
      </form>
    </div>
  </div>
</body>
</html> 