<?php
require_once 'includes/db_connect.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        $_SESSION['forgot_error'] = 'Email is required.';
        header('Location: index.php');
        exit;
    }
    $stmt = $conn->prepare('SELECT id, name FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 120); // 2 minutes
        $stmt2 = $conn->prepare('UPDATE users SET reset_token=?, token_expiry=? WHERE id=?');
        $stmt2->bind_param('ssi', $token, $expiry, $user['id']);
        $stmt2->execute();
        // Send email using SMTP
        require_once 'includes/functions.php';
        $reset_link = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
        $subject = 'Password Reset Request';
        $body = "<p>Hello {$user['name']},</p><p>To reset your password, click the link below (valid for <b>2 minutes</b>):<br><a href='$reset_link'>$reset_link</a></p><p>If you did not request this, ignore this email.</p>";
        $mail_error = '';
        if (send_smtp_mail($email, $user['name'], $subject, $body, $mail_error)) {
            $_SESSION['forgot_success'] = 'A reset link has been sent to your email. Please check your inbox and spam folder. The link is valid for 2 minutes.';
        } else {
            $_SESSION['forgot_error'] = 'Failed to send email. Please try again later.';
        }
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['forgot_success'] = 'If your email is registered, a reset link has been sent.';
        header('Location: index.php');
        exit;
    }
}
header('Location: index.php');
exit; 