<?php
require_once 'includes/db_connect.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $otp_code = trim($_POST['otp_code'] ?? '');
    if (!$user_id || !$otp_code) {
        $_SESSION['otp_error'] = 'OTP required.';
        header('Location: index.php?show_otp=1');
        exit;
    }
    $stmt = $conn->prepare('SELECT id, name, otp_code, is_verified FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && !$user['is_verified'] && $user['otp_code'] === $otp_code) {
        $stmt2 = $conn->prepare('UPDATE users SET is_verified=1, otp_code=NULL WHERE id=?');
        $stmt2->bind_param('i', $user_id);
        $stmt2->execute();
        session_regenerate_id(true);
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        unset($_SESSION['pending_user_id'], $_SESSION['pending_user_otp']);
        $_SESSION['otp_success'] = 'Account verified!';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['otp_error'] = 'Invalid OTP.';
        header('Location: index.php?show_otp=1');
        exit;
    }
}
header('Location: index.php');
exit; 