<?php
require_once '../includes/db_connect.php';
session_start();
$alert = '';
$show_form = false;
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare('SELECT id, token_expiry FROM admins WHERE reset_token=?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    if ($admin && strtotime($admin['token_expiry']) > time()) {
        $show_form = true;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if (!$password || !$confirm) {
                $alert = 'All fields are required.';
            } elseif ($password !== $confirm) {
                $alert = 'Passwords do not match.';
            } elseif (strlen($password) < 6) {
                $alert = 'Password must be at least 6 characters.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt2 = $conn->prepare('UPDATE admins SET password_hash=?, reset_token=NULL, token_expiry=NULL WHERE id=?');
                $stmt2->bind_param('si', $hash, $admin['id']);
                $stmt2->execute();
                $alert = 'Password reset successful. You can now log in.';
                $show_form = false;
            }
        }
    } else {
        $alert = 'Invalid or expired token.';
    }
} else {
    $alert = 'Invalid request.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow p-4">
          <h2 class="mb-4 text-center">Reset Password</h2>
          <?php if ($alert): ?>
            <div class="alert alert-info text-center"><?php echo htmlspecialchars($alert); ?></div>
          <?php endif; ?>
          <?php if ($show_form): ?>
          <form method="post">
            <div class="mb-3">
              <label for="password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="password" name="password" required minlength="6">
            </div>
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-success w-100">Reset Password</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html> 