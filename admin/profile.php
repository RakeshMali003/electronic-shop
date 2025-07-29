<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

$admin_id = $_SESSION['admin_id'];
$msg = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $photo = null;
    $photo_sql = '';

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'admin_' . $admin_id . '_' . time() . '.' . $ext;
        $target = '../uploads/' . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photo = $filename;
            $photo_sql = ', photo=?';
        } else {
            $msg = '<div class="alert alert-danger">Failed to upload photo.</div>';
        }
    }

    // Update admin info
    $sql = "UPDATE admins SET username=?, email=?, mobile=?, address=?$photo_sql WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($photo) {
        $stmt->bind_param('sssssi', $username, $email, $mobile, $address, $photo, $admin_id);
    } else {
        $stmt->bind_param('ssssi', $username, $email, $mobile, $address, $admin_id);
    }
    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success">Profile updated successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Failed to update profile.</div>';
    }
    $stmt->close();
}

// Fetch admin info
$stmt = $conn->prepare('SELECT username, email, mobile, address, photo, is_verified FROM admins WHERE id=? LIMIT 1');
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    die('Admin not found.');
}
$photo_url = $admin['photo'] ? '../uploads/' . $admin['photo'] : 'https://img.icons8.com/ios-filled/100/1a73e8/administrator-male.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile - Electronic Shop</title>
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
    .profile-photo { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #1a73e8; }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container-fluid">
    <div class="row">
      <?php include 'sidebar.php'; ?>
      <main class="col-lg-10 col-md-9 ms-sm-auto px-4 py-5 main-content">
        <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
          <h2 class="mb-4 text-center">Admin Profile</h2>
          <?php echo $msg; ?>
          <div class="mb-3 text-center">
            <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Profile Photo" class="profile-photo mb-2">
          </div>
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="photo" class="form-label">Profile Photo</label>
              <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Name</label>
              <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="mobile" class="form-label">Mobile Number</label>
              <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo htmlspecialchars($admin['mobile']); ?>" pattern="[0-9]{10,15}">
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($admin['address']); ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Verified</label><br>
              <?php if ($admin['is_verified']): ?>
                <span class="badge bg-success">Yes</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">No</span>
              <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
          </form>
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
</body>
</html> 