<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db_connect.php';
$logo = get_setting('logo');
$currency = get_setting('currency', 'INR');
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$all_categories = $categories ? $categories->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Electronic Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    .navbar-pro {
      background: #fff;
      box-shadow: 0 2px 8px rgba(26,115,232,0.06);
      border-bottom: 1px solid #e3e6f0;
      padding-top: 0.5rem;
      padding-bottom: 0.5rem;
    }
    .navbar-pro .navbar-brand {
      color: #1a73e8 !important;
      font-weight: 700;
      font-size: 1.5rem;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .navbar-pro .nav-link {
      color: #222 !important;
      font-weight: 500;
      margin-right: 0.5rem;
    }
    .navbar-pro .nav-link.active, .navbar-pro .nav-link:hover {
      color: #1a73e8 !important;
    }
    .navbar-pro .dropdown-menu {
      border-radius: 10px;
      box-shadow: 0 4px 24px rgba(26,115,232,0.08);
    }
    .navbar-pro .form-control {
      border-radius: 20px;
      min-width: 250px;
    }
    .navbar-pro .btn-search {
      border-radius: 20px;
      background: #1a73e8;
      color: #fff;
      border: none;
      padding: 0.375rem 1.25rem;
      margin-left: -2.5rem;
      z-index: 2;
      position: relative;
    }
    .navbar-pro .icon-btn {
      background: none;
      border: none;
      color: #222;
      font-size: 1.5rem;
      margin-left: 1rem;
      position: relative;
    }
    .navbar-pro .icon-btn .badge {
      position: absolute;
      top: 0;
      right: -8px;
      font-size: 0.7rem;
      background: #e53935;
      color: #fff;
    }
    .navbar-pro .admin-btn {
      margin-left: 1rem;
      border-radius: 8px;
      font-weight: 600;
      border: 1.5px solid #1a73e8;
      color: #1a73e8;
      background: #fff;
      transition: background 0.2s, color 0.2s;
    }
    .navbar-pro .admin-btn:hover {
      background: #1a73e8;
      color: #fff;
      border-color: #1a73e8;
    }
    @media (max-width: 991.98px) {
      .navbar-pro .form-control {
        min-width: 120px;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-pro sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <?php if ($logo): ?>
          <img src="uploads/<?php echo htmlspecialchars($logo); ?>" alt="Logo" width="36" height="36">
        <?php else: ?>
          <img src="https://img.icons8.com/ios-filled/40/1a73e8/electronics.png" alt="Logo" width="36" height="36">
        <?php endif; ?>
        Electronic Shop
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPro" aria-controls="navbarPro" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarPro">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Categories
            </a>
            <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
              <?php foreach ($all_categories as $cat): ?>
                <li><a class="dropdown-item" href="product.php?category=<?php echo $cat['id']; ?>">
                  <?php
                  if ($cat['parent_id']) {
                    foreach ($all_categories as $p) {
                      if ($p['id'] == $cat['parent_id']) {
                        echo htmlspecialchars($p['name']) . ' > ';
                        break;
                      }
                    }
                  }
                  echo htmlspecialchars($cat['name']);
                  ?>
                </a></li>
              <?php endforeach; ?>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
        </ul>
        <form class="d-flex mx-lg-4 my-2 my-lg-0 flex-grow-1 justify-content-center" role="search" style="max-width: 400px;">
          <input class="form-control" type="search" placeholder="Search products..." aria-label="Search">
          <button class="btn btn-search" type="submit"><i class="bi bi-search"></i></button>
        </form>
     
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
        <a href="cart.php" class="icon-btn position-relative" title="Cart">
          <i class="bi bi-cart3"></i>
          <span class="badge rounded-pill"><?php echo get_cart_count(); ?></span>
        </a>
        <span class="ms-2 fw-bold text-primary"><?php echo htmlspecialchars($currency); ?></span>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
          <div class="dropdown d-inline-block ms-2">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <button class="btn btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="bi bi-person me-1"></i>Login / Register</button>
        <?php endif; ?>
        <a href="admin/login.php" class="btn admin-btn ms-2"><i class="bi bi-person-lock me-1"></i>Admin</a>
      </div>
    </div>
  </nav> 

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="loginForm" method="post" action="user_login.php" autocomplete="off">
          <div class="modal-body">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email address</label>
              <input type="email" class="form-control" id="loginEmail" name="email" required autocomplete="username">
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="loginPassword" name="password" required autocomplete="current-password">
            </div>
            <div class="mb-3 text-center">
              <button type="button" class="btn btn-outline-danger w-100 mb-2" id="googleLoginBtn"><i class="bi bi-google me-2"></i>Login with Google</button>
            </div>
            <div class="mb-2 text-center">
              <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Don't have an account? Register</a>
            </div>
            <div class="mb-2 text-center">
              <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Forgot Password?</a>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Create Your Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="registerForm" method="post" action="user_register.php" autocomplete="off">
          <div class="modal-body">
            <div class="mb-3">
              <label for="registerName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="registerName" name="name" required>
            </div>
            <div class="mb-3">
              <label for="registerEmail" class="form-label">Email address</label>
              <input type="email" class="form-control" id="registerEmail" name="email" required>
            </div>
            <div class="mb-3">
              <label for="registerMobile" class="form-label">Mobile Number</label>
              <input type="text" class="form-control" id="registerMobile" name="mobile" required pattern="[0-9]{10}" maxlength="10">
            </div>
            <div class="mb-3">
              <label for="registerPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="registerPassword" name="password" required autocomplete="new-password">
            </div>
            <div class="mb-2 text-center">
              <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Already have an account? Login</a>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success w-100">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- OTP Verification Modal -->
  <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="otpModalLabel">Verify Your Mobile Number</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="otpForm" method="post" action="user_verify_otp.php" autocomplete="off">
          <div class="modal-body">
            <div class="mb-3">
              <label for="otpCode" class="form-label">Enter OTP sent to your mobile</label>
              <input type="text" class="form-control" id="otpCode" name="otp_code" required maxlength="6">
              <input type="hidden" name="user_id" id="otpUserId">
            </div>
            <div class="mb-2 text-center">
              <span id="otpInfo" class="text-muted small"></span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
          </div>
        </form>
      </div>
    </div>
  </div> 

  <!-- Forgot Password Modal -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Your Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="forgotPasswordForm" method="post" action="user_forgot_password.php" autocomplete="off">
          <div class="modal-body">
            <div class="mb-3">
              <label for="forgotEmail" class="form-label">Enter your registered email address</label>
              <input type="email" class="form-control" id="forgotEmail" name="email" required>
            </div>
            <div class="mb-2 text-center">
              <span class="text-muted small">You will receive a password reset link if your email is registered.</span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning w-100">Send Reset Link</button>
          </div>
        </form>
      </div>
    </div>
  </div> 
</body>
<!-- Removed Google Translate scripts and custom trigger --> 