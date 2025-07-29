<?php
require_once 'includes/db_connect.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        $_SESSION['login_error'] = 'Email and password required.';
        header('Location: index.php');
        exit;
    }
    $stmt = $conn->prepare('SELECT id, name, password_hash, is_verified FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        if (!$user['is_verified']) {
            $_SESSION['pending_user_id'] = $user['id'];
            $_SESSION['login_error'] = 'Please verify your account with OTP.';
            header('Location: index.php?show_otp=1');
            exit;
        }
        session_regenerate_id(true);
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['login_error'] = 'Invalid email or password.';
        header('Location: index.php');
        exit;
    }
}
header('Location: index.php');
exit; 