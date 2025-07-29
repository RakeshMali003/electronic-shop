<?php include 'includes/header.php'; ?>

<?php
// Show registration/login/OTP messages
if (isset($_SESSION['register_error'])) {
  echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
  unset($_SESSION['register_error']);
}
if (isset($_SESSION['register_success'])) {
  echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
  unset($_SESSION['register_success']);
}
if (isset($_SESSION['login_error'])) {
  echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
  unset($_SESSION['login_error']);
}
if (isset($_SESSION['otp_error'])) {
  echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['otp_error']) . '</div>';
  unset($_SESSION['otp_error']);
}
if (isset($_SESSION['otp_success'])) {
  echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['otp_success']) . '</div>';
  unset($_SESSION['otp_success']);
}
// Show OTP for demo
if (isset($_SESSION['pending_user_otp'])) {
  echo '<div class="alert alert-info text-center">Demo OTP: <b>' . htmlspecialchars($_SESSION['pending_user_otp']) . '</b></div>';
}
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Auto-show OTP modal if needed
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('show_otp') === '1') {
    var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
    otpModal.show();
    // Autofill user_id
    <?php if (isset($_SESSION['pending_user_id'])): ?>
      document.getElementById('otpUserId').value = '<?php echo $_SESSION['pending_user_id']; ?>';
    <?php endif; ?>
  }
});
</script>

<!-- Hero Section -->
<div class="hero-section text-center">
  <div class="container">
    <h1 class="display-4 mb-3">Big Summer Sale!</h1>
    <p class="lead mb-4">Up to 40% off on select electronic appliances. Shop the best deals now!</p>
    <a href="product.php" class="btn btn-primary btn-lg m-2">Shop Now</a>
    <a href="contact.php" class="btn btn-outline-secondary btn-lg m-2">Contact Us</a>
  </div>
</div>

<!-- Trust Badges -->
<div class="container my-4">
  <div class="row justify-content-center text-center g-4">
    <div class="col-6 col-md-3">
      <img src="https://img.icons8.com/ios-filled/50/1a73e8/verified-account.png" alt="GSTIN" width="40">
      <div class="fw-bold mt-2">GSTIN Registered</div>
    </div>
    <div class="col-6 col-md-3">
      <img src="https://img.icons8.com/ios-filled/50/1a73e8/lock-2.png" alt="Secure Payment" width="40">
      <div class="fw-bold mt-2">100% Secure Payment</div>
    </div>
    <div class="col-6 col-md-3">
      <img src="https://img.icons8.com/ios-filled/50/1a73e8/thumb-up.png" alt="Customer Reviews" width="40">
      <div class="fw-bold mt-2">4.8/5 Customer Reviews</div>
    </div>
    <div class="col-6 col-md-3">
      <img src="https://img.icons8.com/ios-filled/50/1a73e8/delivery.png" alt="Fast Delivery" width="40">
      <div class="fw-bold mt-2">Fast Delivery</div>
    </div>
  </div>
</div>

<!-- Category Highlights -->
<div class="container my-5">
  <h2 class="text-center mb-4">Shop by Category</h2>
  <div class="row g-4">
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Switch and Board Appliance">
        <div class="card-body">
          <h5 class="card-title">Switch & Board Appliance</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Lighting">
        <div class="card-body">
          <h5 class="card-title">Lighting</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Fancy Lights">
        <div class="card-body">
          <h5 class="card-title">Fancy Lights</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Heavy Appliance">
        <div class="card-body">
          <h5 class="card-title">Heavy Appliance</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Geyser">
        <div class="card-body">
          <h5 class="card-title">Geyser</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Fan">
        <div class="card-body">
          <h5 class="card-title">Fan</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1509228468518-180dd4864904?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Pipe and Fittings">
        <div class="card-body">
          <h5 class="card-title">Pipe & Fittings</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Wires">
        <div class="card-body">
          <h5 class="card-title">Wires</h5>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Featured Products -->
<div class="container my-5">
  <h2 class="text-center mb-4">Featured Products</h2>
  <div class="row g-4 justify-content-center">
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Smart LED Bulb">
        <div class="card-body">
          <h5 class="card-title">Smart LED Bulb</h5>
          <p class="card-text text-success fw-bold">₹299 <span class="text-decoration-line-through text-muted">₹399</span></p>
          <a href="#" class="btn btn-sm btn-primary">Add to Cart</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1465101178521-c1a9136a3c5c?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Ceiling Fan">
        <div class="card-body">
          <h5 class="card-title">Ceiling Fan</h5>
          <p class="card-text text-success fw-bold">₹1,499 <span class="text-decoration-line-through text-muted">₹1,799</span></p>
          <a href="#" class="btn btn-sm btn-primary">Add to Cart</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Switch Board">
        <div class="card-body">
          <h5 class="card-title">Switch Board</h5>
          <p class="card-text text-success fw-bold">₹199 <span class="text-decoration-line-through text-muted">₹249</span></p>
          <a href="#" class="btn btn-sm btn-primary">Add to Cart</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center">
        <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Geyser">
        <div class="card-body">
          <h5 class="card-title">Geyser</h5>
          <p class="card-text text-success fw-bold">₹3,499 <span class="text-decoration-line-through text-muted">₹3,999</span></p>
          <a href="#" class="btn btn-sm btn-primary">Add to Cart</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Google Map -->
<div class="container my-5">
  <h2 class="text-center mb-4">Find Us Here</h2>
  <div class="row justify-content-center">
    <div class="col-md-10">
      <div class="card shadow-sm p-0 overflow-hidden mb-4">
        <div class="row g-0 align-items-stretch">
          <div class="col-lg-7">
            <div class="ratio ratio-4x3 h-100">
              <iframe src="https://maps.google.com/maps?q=25.5941,85.1376&z=15&output=embed" style="border:0; min-height:300px; width:100%; height:100%;" allowfullscreen="" loading="lazy"></iframe>
            </div>
          </div>
          <div class="col-lg-5 bg-light d-flex flex-column justify-content-center p-4">
            <h4 class="fw-bold mb-3 text-primary"><i class="bi bi-geo-alt me-2"></i>Our Location</h4>
            <div class="mb-2"><i class="bi bi-geo me-2"></i>123 Main Street, Patna, Bihar, India</div>
            <div class="mb-2"><i class="bi bi-envelope me-2"></i>info@electronicshop.com</div>
            <div class="mb-2"><i class="bi bi-telephone me-2"></i>+91 9876543210</div>
            <div class="mt-3">
              <a href="https://maps.google.com/maps?q=25.5941,85.1376&z=15&output=embed" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-map me-1"></i>View on Google Maps</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <hr class="my-5"/>
</div>

<?php include 'includes/footer.php'; ?> 