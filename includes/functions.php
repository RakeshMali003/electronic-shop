<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';

function get_products($category_id = null) {
    global $conn;
    $sql = "SELECT * FROM products WHERE status='active'";
    if ($category_id) {
        $sql .= " AND category_id=" . intval($category_id);
    }
    $sql .= " ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[$row['id']] = $row;
    }
    return $products;
}

function get_product($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=? AND status='active'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function add_to_cart($product_id, $qty = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function remove_from_cart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function get_cart_count() {
    if (!isset($_SESSION['cart'])) return 0;
    return array_sum($_SESSION['cart']);
}

function get_cart_items() {
    $items = [];
    if (!isset($_SESSION['cart'])) return $items;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $product = get_product($pid);
        if ($product) {
            $item = $product;
            $item['id'] = $pid;
            $item['qty'] = $qty;
            $item['total'] = $item['price'] * $qty;
            $items[] = $item;
        }
    }
    return $items;
}

function get_setting($key, $default = '') {
    global $conn;
    $stmt = $conn->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?');
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $stmt->bind_result($val);
    $stmt->fetch();
    $stmt->close();
    return $val !== null ? $val : $default;
}

// SMTP mail function using PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_smtp_mail($to, $to_name, $subject, $body, &$error = null) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mail = new PHPMailer(true);
    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // <-- PUT YOUR GMAIL HERE
        $mail->Password = 'your_app_password';    // <-- PUT YOUR GMAIL APP PASSWORD HERE
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('your_email@gmail.com', 'Electronic Shop'); // <-- PUT YOUR GMAIL HERE
        $mail->addAddress($to, $to_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        $mail->send();
        return true;
    } catch (Exception $e) {
        $error = $mail->ErrorInfo;
        return false;
    }
}

// Test function for email sending (remove after testing)
function test_smtp_mail() {
    $error = '';
    $result = send_smtp_mail('rakeshmali46519@gmail.com', 'Rakesh', 'Test Email', '<p>This is a test email from Electronic Shop.</p>', $error);
    if ($result) {
        echo 'Test email sent successfully!';
    } else {
        echo 'Test email failed: ' . $error;
    }
}

function get_related_products($product_id, $category_id = null, $limit = 4) {
    global $conn;
    if (!$category_id) return [];
    $stmt = $conn->prepare("SELECT * FROM products WHERE status='active' AND category_id=? AND id!=? ORDER BY RAND() LIMIT ?");
    $stmt->bind_param('iii', $category_id, $product_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $related = [];
    while ($row = $result->fetch_assoc()) {
        $related[] = $row;
    }
    return $related;
}

function get_product_rating($product_id) {
    global $conn;
    $stmt = $conn->prepare('SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM product_reviews WHERE product_id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return [
        'avg' => round(floatval($row['avg_rating']), 1),
        'count' => intval($row['total_reviews'])
    ];
}
?> 