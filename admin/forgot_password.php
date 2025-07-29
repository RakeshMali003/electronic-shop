<?php
require_once '../includes/db_connect.php';
session_start();
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        $alert = 'Email is required.';
    } else {
        $stmt = $conn->prepare('SELECT id, username FROM admins WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        if ($admin) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + 120); // 2 minutes
            $stmt2 = $conn->prepare('UPDATE admins SET reset_token=?, token_expiry=? WHERE id=?');
            $stmt2->bind_param('ssi', $token, $expiry, $admin['id']);
            $stmt2->execute();
            // Send email using SMTP
            require_once '../includes/functions.php';
            $reset_link = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
            $subject = 'Admin Password Reset Request';
            $body = "<p>Hello {$admin['username']},</p><p>To reset your password, click the link below (valid for <b>2 minutes</b>):<br><a href='$reset_link'>$reset_link</a></p><p>If you did not request this, ignore this email.</p>";
            $mail_error = '';
            if (send_smtp_mail($email, $admin['username'], $subject, $body, $mail_error)) {
                $alert = 'A reset link has been sent to your email. Please check your inbox and spam folder. The link is valid for 2 minutes.';
            } else {
                $alert = 'Failed to send email. Please try again later.';
            }
        } else {
            $alert = 'If your email is registered, a reset link has been sent.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow p-4">
          <h2 class="mb-4 text-center">Forgot Password</h2>
          <?php if ($alert): ?>
            <div class="alert alert-info text-center"><?php echo htmlspecialchars($alert); ?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label for="email" class="form-label">Enter your registered email address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-2 text-center">
              <span class="text-muted small">You will receive a password reset link if your email is registered.</span>
            </div>
            <button type="submit" class="btn btn-warning w-100">Send Reset Link</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html> 