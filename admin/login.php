<?php
session_start();
require_once '../includes/db_connect.php';
$error = '';
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $status = 'fail';
    $admin_id = null;
    // Fetch admin by username or email
    $stmt = $conn->prepare("SELECT id, username, password_hash, is_verified, otp_code FROM admins WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    if ($admin) {
        $admin_id = $admin['id'];
        if (password_verify($password, $admin['password_hash'])) {
            if (!$admin['is_verified']) {
                // Generate OTP and update
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $stmt2 = $conn->prepare('UPDATE admins SET otp_code=? WHERE id=?');
                $stmt2->bind_param('si', $otp, $admin_id);
                $stmt2->execute();
                $_SESSION['admin_pending_id'] = $admin_id;
                $_SESSION['admin_pending_otp'] = $otp;
                $error = 'OTP sent (demo: ' . $otp . '). Please verify.';
                // Log attempt
                $stmt3 = $conn->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, status, user_agent) VALUES (?, ?, ?, ?)');
                $stmt3->bind_param('isss', $admin_id, $ip, $status, $user_agent);
                $stmt3->execute();
            } else {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_id'] = $admin_id;
                $status = 'success';
                // Log attempt
                $stmt3 = $conn->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, status, user_agent) VALUES (?, ?, ?, ?)');
                $stmt3->bind_param('isss', $admin_id, $ip, $status, $user_agent);
                $stmt3->execute();
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $error = 'Invalid username/email or password.';
            // Log attempt
            $stmt3 = $conn->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, status, user_agent) VALUES (?, ?, ?, ?)');
            $stmt3->bind_param('isss', $admin_id, $ip, $status, $user_agent);
            $stmt3->execute();
        }
    } else {
        $error = 'Invalid username/email or password.';
        // Log attempt (no admin_id)
        $stmt3 = $conn->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, status, user_agent) VALUES (?, ?, ?, ?)');
        $null = null;
        $stmt3->bind_param('isss', $null, $ip, $status, $user_agent);
        $stmt3->execute();
    }
}
// OTP verification
if (isset($_POST['otp_verify']) && isset($_SESSION['admin_pending_id'])) {
    $admin_id = $_SESSION['admin_pending_id'];
    $otp_code = $_POST['otp_code'] ?? '';
    $stmt = $conn->prepare('SELECT otp_code FROM admins WHERE id=?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->bind_result($db_otp);
    $stmt->fetch();
    $stmt->close();
    if ($db_otp && $db_otp === $otp_code) {
        $stmt2 = $conn->prepare('UPDATE admins SET is_verified=1, otp_code=NULL WHERE id=?');
        $stmt2->bind_param('i', $admin_id);
        $stmt2->execute();
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin_id;
        unset($_SESSION['admin_pending_id'], $_SESSION['admin_pending_otp']);
        // Log success
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $status = 'success';
        $stmt3 = $conn->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, status, user_agent) VALUES (?, ?, ?, ?)');
        $stmt3->bind_param('isss', $admin_id, $ip, $status, $user_agent);
        $stmt3->execute();
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid OTP.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Electronic Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow p-4">
          <h2 class="mb-4 text-center">Admin Login</h2>
          <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <?php if (isset($_SESSION['admin_pending_id'])): ?>
            <form method="post">
              <div class="mb-3">
                <label for="otp_code" class="form-label">Enter OTP (sent to your mobile/email)</label>
                <input type="text" class="form-control" id="otp_code" name="otp_code" required maxlength="6">
              </div>
              <button type="submit" name="otp_verify" class="btn btn-primary w-100">Verify OTP</button>
            </form>
          <?php else: ?>
            <form method="post">
              <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="mb-2 text-center">
                <a href="forgot_password.php">Forgot Password?</a>
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html> 