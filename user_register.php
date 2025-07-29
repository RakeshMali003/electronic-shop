<?php
require_once 'includes/db_connect.php';
session_start();

function generate_otp($length = 6) {
    return str_pad(random_int(0, pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$mobile || !$password) {
        $_SESSION['register_error'] = 'All fields are required.';
        header('Location: index.php');
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = 'Invalid email address.';
        header('Location: index.php');
        exit;
    }
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $_SESSION['register_error'] = 'Invalid mobile number.';
        header('Location: index.php');
        exit;
    }
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = 'Email already registered.';
        header('Location: index.php');
        exit;
    }
    $stmt->close();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $otp = generate_otp();
    $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash, mobile, otp_code, is_verified) VALUES (?, ?, ?, ?, ?, 0)');
    $stmt->bind_param('sssss', $name, $email, $password_hash, $mobile, $otp);
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $_SESSION['pending_user_id'] = $user_id;
        $_SESSION['pending_user_otp'] = $otp;
        $_SESSION['register_success'] = 'Registration successful! Please verify OTP.';
        header('Location: index.php?show_otp=1');
        exit;
    } else {
        $_SESSION['register_error'] = 'Registration failed. Please try again.';
        header('Location: index.php');
        exit;
    }
}
header('Location: index.php');
exit; 