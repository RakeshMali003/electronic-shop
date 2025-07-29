<?php
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';
$currency = get_setting('currency', 'INR');
$tax_rate = floatval(get_setting('tax_rate', '18'));
$shipping_options = get_setting('shipping_options');
$contact_email = get_setting('contact_email', 'info@electronicshop.com');
$contact_phone = get_setting('contact_phone', '+91-1234567890');
$razorpay_key = get_setting('payment_gateway_key', 'rzp_test_YourTestKeyHere'); // Use your real key in production
?>
<?php include 'includes/header.php'; ?>

<div class="container my-5">
  <h2 class="text-center mb-4">Checkout</h2>
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card shadow-sm p-4">
        <h4 class="mb-3">Shipping Details</h4>
        <form>
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Your Name">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" rows="3" placeholder="Your Address"></textarea>
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contact" placeholder="Contact Number">
          </div>
          <div class="mb-3">
            <label for="payment" class="form-label">Payment Method</label>
            <select class="form-select" id="payment">
              <option selected>Cash on Delivery</option>
              <option>UPI</option>
              <option>Credit/Debit Card</option>
            </select>
          </div>
          <button type="button" class="btn btn-success w-100" id="rzp-button">Pay with Razorpay</button>
        </form>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card shadow-sm p-4">
        <h4>Order Summary</h4>
        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Smart LED Bulb x 1
            <span>₹299</span>
          </li>
          <!-- Repeat for more products -->
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Subtotal
            <span><?php echo htmlspecialchars($currency); ?>299</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            GST (<?php echo $tax_rate; ?>%)
            <span><?php echo htmlspecialchars($currency); ?>54</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
            Total
            <span><?php echo htmlspecialchars($currency); ?>353</span>
          </li>
        </ul>
        <div class="mb-2">
          <strong>Shipping Options:</strong><br>
          <?php if ($shipping_options):
            foreach (explode("\n", $shipping_options) as $opt) {
              $parts = explode('|', $opt);
              if (count($parts) == 2) {
                echo htmlspecialchars(trim($parts[0])) . ' (' . htmlspecialchars($currency) . htmlspecialchars(trim($parts[1])) . ')<br>';
              }
            }
          else:
            echo 'Standard (Free)';
          endif; ?>
        </div>
        <div class="mt-2 small text-muted">
          For support: <?php echo htmlspecialchars($contact_email); ?> | <?php echo htmlspecialchars($contact_phone); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-button').onclick = function(e) {
  e.preventDefault();
  var options = {
    "key": "<?php echo $razorpay_key; ?>",
    "amount": 353 * 100, // Example: total in paise
    "currency": "<?php echo $currency; ?>",
    "name": "Electronic Shop",
    "description": "Order Payment",
    "handler": function (response){
      // Send payment ID to backend for verification and order placement
      fetch('place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'payment_id=' + encodeURIComponent(response.razorpay_payment_id)
      })
      .then(res => res.text())
      .then(msg => {
        alert(msg);
        window.location.href = 'profile.php'; // Redirect to profile or order history
      });
    },
    "prefill": {
      "email": "<?php echo htmlspecialchars($contact_email); ?>",
      "contact": "<?php echo htmlspecialchars($contact_phone); ?>"
    },
    "theme": {"color": "#1a73e8"}
  };
  var rzp1 = new Razorpay(options);
  rzp1.open();
}
</script> 