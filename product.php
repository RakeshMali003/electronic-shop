<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/functions.php';
// Fetch categories
require_once 'includes/db_connect.php';
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$all_categories = $categories ? $categories->fetch_all(MYSQLI_ASSOC) : [];
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : null;
if (isset($_GET['add_to_cart'])) {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        echo '<script>var loginModal = new bootstrap.Modal(document.getElementById("loginModal")); loginModal.show();</script>';
        echo '<div class="alert alert-warning text-center">Please login to add products to your cart.</div>';
    } else {
        $pid = intval($_GET['add_to_cart']);
        add_to_cart($pid);
        echo '<div class="alert alert-success text-center">Product added to cart!</div>';
    }
}
$products = get_products($selected_category);
?>
<div class="container my-5">
  <div class="row">
    <!-- Sidebar Filters -->
    <aside class="col-lg-3 mb-4">
      <div class="card mb-4">
        <div class="card-header bg-white fw-bold">Categories</div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item <?php if (!$selected_category) echo 'active'; ?>">
            <a href="product.php" class="text-decoration-none text-dark">All Categories</a>
          </li>
          <?php foreach ($all_categories as $cat): ?>
            <li class="list-group-item <?php if ($selected_category == $cat['id']) echo 'active'; ?>">
              <a href="product.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark">
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
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="card mb-4">
        <div class="card-header bg-white fw-bold">Price Range</div>
        <div class="card-body">
          <input type="range" class="form-range" min="100" max="5000" step="100" id="priceRange">
          <div class="d-flex justify-content-between">
            <span>₹100</span>
            <span>₹5000+</span>
          </div>
        </div>
      </div>
    </aside>
    <!-- Product Grid -->
    <main class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">All Products<?php if ($selected_category) { echo ' - '; foreach ($all_categories as $cat) { if ($cat['id'] == $selected_category) { echo htmlspecialchars($cat['name']); break; } } } ?></h2>
        <div>
          <select class="form-select" onchange="location = this.value;">
            <option value="product.php" <?php if (!$selected_category) echo 'selected'; ?>>All Categories</option>
            <?php foreach ($all_categories as $cat): ?>
              <option value="product.php?category=<?php echo $cat['id']; ?>" <?php if ($selected_category == $cat['id']) echo 'selected'; ?>>
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
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="row g-4">
        <?php if (empty($products)): ?>
          <div class="col-12"><div class="alert alert-info text-center">No products found.</div></div>
        <?php else: ?>
          <?php foreach ($products as $product): ?>
            <div class="col-md-4 col-sm-6 col-12">
              <div class="card h-100 shadow-sm border-0 product-card" style="transition: box-shadow 0.2s;">
                <div class="ratio ratio-4x3">
                  <img src="<?php echo $product['main_image'] ? 'uploads/' . $product['main_image'] : 'https://via.placeholder.com/400x300?text=No+Image'; ?>" class="card-img-top object-fit-cover" alt="<?php echo htmlspecialchars($product['name']); ?>" style="object-fit:cover; width:100%; height:100%;">
                </div>
                <div class="card-body text-center">
                  <h5 class="card-title mb-1" style="font-size:1.1rem; font-weight:600; min-height:2.5em;">
                    <?php echo htmlspecialchars($product['name']); ?>
                  </h5>
                  <?php if (!empty($product['short_description'])): ?>
                    <p class="card-text text-muted small mb-2" style="min-height:2.5em;">
                      <?php echo htmlspecialchars($product['short_description']); ?>
                    </p>
                  <?php endif; ?>
                  <p class="card-text text-success fw-bold mb-2" style="font-size:1.1rem;">₹<?php echo $product['price']; ?></p>
                  <span class="badge rounded-pill <?php echo $product['quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?> mb-3" style="font-size:0.9em; letter-spacing:0.5px;">
                    <?php echo $product['quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                  </span>
                  <div>
                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm px-3">View Details</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?> 
<style>
  .product-card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.12)!important;
    transform: translateY(-2px) scale(1.01);
  }
  .object-fit-cover {
    object-fit: cover;
  }
</style> 