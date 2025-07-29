<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = get_product($product_id); // Use the correct function
if (!$product) {
  echo '<div class="container my-5"><div class="alert alert-danger text-center">Product not found.</div></div>';
  include 'includes/footer.php';
  exit;
}
// Optionally fetch related products
$related_products = get_related_products($product_id, $product['category_id'] ?? null);
$rating = get_product_rating($product_id);
?>
<div class="product-details-hero-full d-flex justify-content-center align-items-center py-5 px-2 px-md-4" style="background:#f4f6fa; min-height:100vh;">
  <div class="product-hero-full d-flex flex-wrap flex-lg-nowrap w-100 position-relative" style="max-width:1800px; min-height:520px;">
    <!-- Angled Yellow Accent -->
    <div class="product-accent-full position-absolute top-0 start-0" style="width:55%; height:100%; background:linear-gradient(120deg,#ffe259 60%,#fff 60%); clip-path:polygon(0 0,100% 0,60% 100%,0% 100%); z-index:1;"></div>
    <!-- Main Image -->
    <div class="product-hero-image-col d-flex flex-column align-items-center justify-content-center flex-shrink-0 p-4 position-relative" style="min-width:340px; min-height:420px; z-index:2;">
      <img id="mainProductImage" src="<?php echo $product['main_image'] ? 'uploads/' . $product['main_image'] : 'https://via.placeholder.com/400x350?text=No+Image'; ?>" class="img-fluid product-hero-main-img-full" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height:340px; max-width:340px; object-fit:contain; background:#fff; position:relative; z-index:2;">
      <?php
        $gallery = [];
        for ($i = 1; $i <= 5; $i++) {
          if (!empty($product['image_' . $i])) {
            $gallery[] = $product['image_' . $i];
          }
        }
      ?>
      <?php if (!empty($gallery)): ?>
      <div class="d-flex flex-nowrap overflow-auto gap-2 pt-3 pb-1 px-1 w-100 justify-content-center">
        <img src="<?php echo $product['main_image'] ? 'uploads/' . $product['main_image'] : 'https://via.placeholder.com/80x60?text=No+Image'; ?>" class="img-thumbnail thumb-img-hero-full" style="width: 60px; height: 50px; object-fit:contain; background:#fff; cursor:pointer; border:2px solid #ffe259;" alt="Thumb" onclick="changeMainImage(this)">
        <?php foreach ($gallery as $img): ?>
          <img src="uploads/<?php echo htmlspecialchars($img); ?>" class="img-thumbnail thumb-img-hero-full" style="width: 60px; height: 50px; object-fit:contain; background:#fff; cursor:pointer;" alt="Thumb" onclick="changeMainImage(this)">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <!-- Product Info -->
    <div class="product-hero-info-col flex-grow-1 p-4 d-flex flex-column justify-content-between" style="min-width:260px; z-index:2; background:transparent;">
      <div>
        <div class="d-flex align-items-center mb-2">
          <img src="https://img.icons8.com/ios-filled/40/1a73e8/electronics.png" alt="Logo" width="32" height="32" class="me-2 d-none d-lg-block">
          <h2 class="fw-bold mb-0" style="font-size:2rem; color:#222; letter-spacing:1px;"> <?php echo htmlspecialchars($product['name']); ?> </h2>
        </div>
        <div class="mb-1 fs-5 text-muted"><?php echo htmlspecialchars($product['subtitle'] ?? ''); ?></div>
        <div class="mb-2 d-flex align-items-center gap-2">
          <span class="text-warning fs-5">
            <?php
              $fullStars = floor($rating['avg']);
              $halfStar = ($rating['avg'] - $fullStars) >= 0.5 ? 1 : 0;
              $emptyStars = 5 - $fullStars - $halfStar;
              for ($i = 0; $i < $fullStars; $i++) echo '<i class="bi bi-star-fill"></i>';
              if ($halfStar) echo '<i class="bi bi-star-half"></i>';
              for ($i = 0; $i < $emptyStars; $i++) echo '<i class="bi bi-star"></i>';
            ?>
          </span>
          <span class="text-muted small">(<?php echo $rating['count']; ?> reviews)</span>
        </div>
        <div class="mb-2 fw-bold">INFINITE SUPPORT. TOTAL CONTROL.</div>
        <div class="mb-3 text-muted" style="font-size:1rem; line-height:1.6;">
          <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </div>
        <div class="mb-3 d-flex align-items-center gap-2">
          <form method="post" action="wishlist.php" class="d-inline">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit" class="btn btn-outline-warning btn-sm px-2 py-1" title="Add to Wishlist"><i class="bi bi-heart"></i></button>
          </form>
          <form method="post" action="cart.php" class="d-inline ms-1">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm px-2 py-1" title="Add to Cart"><i class="bi bi-cart"></i></button>
          </form>
        </div>
        <div class="mb-3">
          <span class="fw-semibold me-2">Select Size</span>
          <?php foreach ([7,8,9,10,11] as $size): ?>
            <span class="size-box-hero-full"><?php echo $size; ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="d-flex flex-column align-items-start mt-3">
        <div class="fs-1 fw-bold mb-2" style="color:#222;">₹<?php echo $product['price']; ?></div>
        <form method="post" action="cart.php" class="w-100">
          <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
          <button type="submit" name="buy_now" class="btn btn-lg w-100 fw-bold shadow-sm" style="background:#ff6600; color:#fff; font-size:1.1rem; border-radius:12px;">BUY</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Related Products -->
<div class="container-lg mt-5">
  <?php if (!empty($related_products)): ?>
  <h3 class="mb-4 fw-bold" style="color:#1a73e8;">Related Products</h3>
  <div class="row g-4 justify-content-center">
    <?php foreach ($related_products as $rel): ?>
    <div class="col-md-3 col-6">
      <div class="card h-100 text-center shadow-sm border-0 product-card" style="transition: box-shadow 0.2s;">
        <div class="ratio ratio-4x3">
          <img src="<?php echo $rel['main_image'] ? 'uploads/' . $rel['main_image'] : 'https://via.placeholder.com/400x300?text=No+Image'; ?>" class="card-img-top object-fit-contain" alt="<?php echo htmlspecialchars($rel['name']); ?>" style="object-fit:contain; background:#fff;">
        </div>
        <div class="card-body">
          <h6 class="card-title fw-bold" style="min-height:2.2em; font-size:1.1em; color:#1a73e8;"> <?php echo htmlspecialchars($rel['name']); ?> </h6>
          <p class="card-text text-success fw-bold mb-2">₹<?php echo $rel['price']; ?></p>
          <a href="product_details.php?id=<?php echo $rel['id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<style>
  .product-details-hero-full { background: #f4f6fa; min-height: 100vh; }
  .product-hero-full { min-height:520px; border-radius:0; box-shadow:none; }
  .product-accent-full { border-radius: 0 0 120px 0/0 0 120px 0; }
  .product-hero-main-img-full { border-radius: 18px; background:#fff; box-shadow:none; }
  .thumb-img-hero-full { border: 2px solid #ffe259; transition: border 0.2s, box-shadow 0.2s; border-radius: 10px; box-shadow:none; }
  .thumb-img-hero-full.active, .thumb-img-hero-full:hover { border: 2px solid #1a73e8 !important; box-shadow: 0 0 0 2px #b3d1ff; }
  .color-dot-hero-full { display:inline-block; width:20px; height:20px; border-radius:50%; margin-right:6px; border:2px solid #eee; vertical-align:middle; }
  .size-box-hero-full { display:inline-block; width:32px; height:32px; border-radius:8px; background:#f5f5f5; color:#222; text-align:center; line-height:32px; font-weight:600; margin-right:6px; border:2px solid #eee; font-size:1rem; cursor:pointer; transition: border 0.2s; }
  .size-box-hero-full:hover { border:2px solid #1a73e8; }
  .product-card:hover { box-shadow: 0 0.5rem 1.5rem rgba(26,115,232,0.13)!important; transform: translateY(-2px) scale(1.01); }
  @media (max-width: 991.98px) {
    .product-hero-full { flex-direction: column !important; border-radius: 0; }
    .product-hero-image-col, .product-hero-info-col { min-width:0; width:100% !important; border-radius: 0 !important; }
    .product-accent-full { width:100%; height:220px; clip-path:polygon(0 0,100% 0,100% 100%,0 60%); border-radius:0 0 80px 0/0 0 80px 0; }
    .product-hero-main-img-full { max-width:90vw; }
  }
  @media (max-width: 575.98px) {
    .product-hero-full { padding: 0 !important; }
    .product-hero-image-col, .product-hero-info-col { padding: 1.2rem !important; }
    .product-hero-main-img-full { max-height:180px; }
    h2, .fw-bold { font-size:1.2rem !important; }
    .fs-1 { font-size:1.3rem !important; }
  }
</style>
<script>
  function changeMainImage(thumb) {
    var mainImg = document.getElementById('mainProductImage');
    mainImg.src = thumb.src;
    document.querySelectorAll('.thumb-img-hero-full').forEach(function(img){ img.classList.remove('active'); });
    thumb.classList.add('active');
  }
</script>
<?php include 'includes/footer.php'; ?> 