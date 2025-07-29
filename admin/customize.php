<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

// Helper: get setting
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
// Helper: set setting
function set_setting($key, $value) {
    global $conn;
    $stmt = $conn->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=?');
    $stmt->bind_param('sss', $key, $value, $value);
    $stmt->execute();
    $stmt->close();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_setting('navbar_links', $_POST['navbar_links'] ?? '');
    set_setting('main_title', $_POST['main_title'] ?? '');
    set_setting('main_subtitle', $_POST['main_subtitle'] ?? '');
    // Handle hero image upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
        $ext = pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('hero_') . '.' . $ext;
        move_uploaded_file($_FILES['hero_image']['tmp_name'], '../uploads/' . $filename);
        set_setting('hero_image', $filename);
    }
    set_setting('shop_categories', $_POST['shop_categories'] ?? '');
    set_setting('featured_products', $_POST['featured_products'] ?? '');
    set_setting('address', $_POST['address'] ?? '');
    set_setting('map_link', $_POST['map_link'] ?? '');
    set_setting('facebook', $_POST['facebook'] ?? '');
    set_setting('instagram', $_POST['instagram'] ?? '');
    set_setting('twitter', $_POST['twitter'] ?? '');
    set_setting('about_us', $_POST['about_us'] ?? '');
    set_setting('contact_us', $_POST['contact_us'] ?? '');
    set_setting('navbar_color', $_POST['navbar_color'] ?? '#1a73e8');
    set_setting('footer_color', $_POST['footer_color'] ?? '#ffffff');
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_') . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], '../uploads/' . $filename);
        set_setting('logo', $filename);
    }
    set_setting('contact_email', $_POST['contact_email'] ?? '');
    set_setting('contact_phone', $_POST['contact_phone'] ?? '');
    set_setting('payment_gateway_key', $_POST['payment_gateway_key'] ?? '');
    set_setting('order_notification_email', $_POST['order_notification_email'] ?? '');
    set_setting('tax_rate', $_POST['tax_rate'] ?? '18');
    set_setting('shipping_options', $_POST['shipping_options'] ?? '');
    set_setting('currency', $_POST['currency'] ?? 'INR');
    $msg = '<div class="alert alert-success">Settings saved!</div>';
}
// Load settings
$navbar_links = get_setting('navbar_links');
$main_title = get_setting('main_title');
$main_subtitle = get_setting('main_subtitle');
$hero_image = get_setting('hero_image');
$shop_categories = get_setting('shop_categories');
$featured_products = get_setting('featured_products');
$address = get_setting('address');
$map_link = get_setting('map_link');
$facebook = get_setting('facebook');
$instagram = get_setting('instagram');
$twitter = get_setting('twitter');
$about_us = get_setting('about_us');
$contact_us = get_setting('contact_us');
$navbar_color = get_setting('navbar_color', '#1a73e8');
$footer_color = get_setting('footer_color', '#ffffff');
// Add new settings
$logo = get_setting('logo');
$contact_email = get_setting('contact_email');
$contact_phone = get_setting('contact_phone');
$payment_gateway_key = get_setting('payment_gateway_key');
$order_notification_email = get_setting('order_notification_email');
$tax_rate = get_setting('tax_rate', '18');
$shipping_options = get_setting('shipping_options');
$currency = get_setting('currency', 'INR');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Website Customization - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background: #f8f9fa; }
    .admin-navbar { background: #1a73e8; color: #fff; box-shadow: 0 2px 8px rgba(26,115,232,0.08); }
    .main-content { margin-left: 220px; padding: 2rem 2rem 2rem 2rem; }
    @media (max-width: 991.98px) { .main-content { margin-left: 0; padding: 1rem; } }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container-fluid">
    <div class="row">
      <?php include 'sidebar.php'; ?>
      <main class="col-lg-10 col-md-9 ms-sm-auto px-4 py-5 main-content">
        <h2 class="mb-4">Website Customization</h2>
        <?php if (!empty($msg)) echo $msg; ?>
        <form method="post" enctype="multipart/form-data">
          <div class="card mb-4">
            <div class="card-header fw-bold">Navbar Items</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Navbar Links (comma separated)</label>
                <input type="text" name="navbar_links" class="form-control" value="<?php echo htmlspecialchars($navbar_links); ?>" placeholder="Home, Products, About Us, Contact Us">
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Main Section (Hero)</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Main Title</label>
                <input type="text" name="main_title" class="form-control" value="<?php echo htmlspecialchars($main_title); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Main Subtitle</label>
                <input type="text" name="main_subtitle" class="form-control" value="<?php echo htmlspecialchars($main_subtitle); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Hero Image</label>
                <input type="file" name="hero_image" class="form-control">
                <?php if ($hero_image): ?>
                  <img src="../uploads/<?php echo htmlspecialchars($hero_image); ?>" class="img-thumbnail mt-2" width="120">
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Shop by Category</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Categories (one per line, format: Name|Image URL)</label>
                <textarea name="shop_categories" class="form-control" rows="4" placeholder="Lighting|img1.jpg\nFan|img2.jpg"><?php echo htmlspecialchars($shop_categories); ?></textarea>
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Featured Products</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Product IDs (comma separated)</label>
                <input type="text" name="featured_products" class="form-control" value="<?php echo htmlspecialchars($featured_products); ?>" placeholder="1,2,3">
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Find Us Here</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Google Maps Embed Link</label>
                <input type="text" name="map_link" class="form-control" value="<?php echo htmlspecialchars($map_link); ?>">
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Social Media Links</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Facebook</label>
                <input type="text" name="facebook" class="form-control" value="<?php echo htmlspecialchars($facebook); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Instagram</label>
                <input type="text" name="instagram" class="form-control" value="<?php echo htmlspecialchars($instagram); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Twitter</label>
                <input type="text" name="twitter" class="form-control" value="<?php echo htmlspecialchars($twitter); ?>">
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">About Us</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">About Us Content</label>
                <textarea name="about_us" class="form-control" rows="3"><?php echo htmlspecialchars($about_us); ?></textarea>
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Contact Us</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Contact Us Content</label>
                <textarea name="contact_us" class="form-control" rows="3"><?php echo htmlspecialchars($contact_us); ?></textarea>
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Colors</div>
            <div class="card-body row g-3">
              <div class="col-md-6">
                <label class="form-label">Navbar Color</label>
                <input type="color" name="navbar_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($navbar_color); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Footer Color</label>
                <input type="color" name="footer_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($footer_color); ?>">
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Store Logo & Contact</div>
            <div class="card-body row g-3">
              <div class="col-md-6">
                <label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-control">
                <?php if ($logo): ?>
                  <img src="../uploads/<?php echo htmlspecialchars($logo); ?>" class="img-thumbnail mt-2" width="120">
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($contact_email); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($contact_phone); ?>">
              </div>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header fw-bold">Ecommerce Settings</div>
            <div class="card-body row g-3">
              <div class="col-md-6">
                <label class="form-label">Payment Gateway Key</label>
                <input type="text" name="payment_gateway_key" class="form-control" value="<?php echo htmlspecialchars($payment_gateway_key); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Order Notification Email</label>
                <input type="email" name="order_notification_email" class="form-control" value="<?php echo htmlspecialchars($order_notification_email); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" name="tax_rate" class="form-control" value="<?php echo htmlspecialchars($tax_rate); ?>" min="0" max="100">
              </div>
              <div class="col-md-4">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($currency); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Shipping Options (one per line, format: Name|Price)</label>
                <textarea name="shipping_options" class="form-control" rows="2" placeholder="Standard|50\nExpress|100"><?php echo htmlspecialchars($shipping_options); ?></textarea>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 