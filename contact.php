<?php
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';
$contact_email = get_setting('contact_email', 'info@electronicshop.com');
$contact_phone = get_setting('contact_phone', '+91-1234567890');
$address = get_setting('address', 'Main Road, City Center, Your City');
$map_link = get_setting('map_link', 'https://maps.google.com/maps?q=25.5941,85.1376&z=15&output=embed');
?>
<?php include 'includes/header.php'; ?>

<!-- Contact Hero -->
<div class="container-fluid py-5 bg-light mb-5">
  <div class="container text-center">
    <h1 class="display-5 fw-bold">Contact Us</h1>
    <p class="lead">We'd love to hear from you! Reach out for any queries, feedback, or support.</p>
  </div>
</div>

<div class="container mb-5">
  <div class="row g-5">
    <div class="col-md-6">
      <div class="card shadow-sm p-4">
        <h4 class="mb-3">Send Us a Message</h4>
        <form>
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Your Name">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Your Email">
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" placeholder="Subject">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" rows="4" placeholder="Your Message"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm p-4 mb-4">
        <h5>Store Information</h5>
        <p class="mb-1"><i class="bi bi-geo-alt-fill text-primary"></i> <?php echo htmlspecialchars($address); ?></p>
        <p class="mb-1"><i class="bi bi-telephone-fill text-primary"></i> <?php echo htmlspecialchars($contact_phone); ?></p>
        <p class="mb-1"><i class="bi bi-envelope-fill text-primary"></i> <?php echo htmlspecialchars($contact_email); ?></p>
      </div>
      <div class="rounded overflow-hidden shadow-sm">
        <iframe src="<?php echo htmlspecialchars($map_link); ?>" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?> 