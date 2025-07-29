<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    add_to_cart(intval($_POST['product_id']));
    header('Location: cart.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now']) && isset($_POST['product_id'])) {
    add_to_cart(intval($_POST['product_id']));
    header('Location: checkout.php');
    exit;
}
include 'includes/header.php';

$cart_items = get_cart_items();
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['total'];
}
$gst = round($subtotal * 0.18);
$total = $subtotal + $gst;
// Remove item
if (isset($_GET['remove'])) {
    remove_from_cart(intval($_GET['remove']));
    header('Location: cart.php');
    exit;
}
?>
<div class="container my-5">
  <h2 class="text-center mb-4">Your Cart</h2>
  <?php if (empty($cart_items)): ?>
    <div class="alert alert-info text-center">Your cart is empty.</div>
  <?php else: ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img src="<?php echo $item['main_image'] ? 'uploads/' . $item['main_image'] : 'https://via.placeholder.com/60x60?text=No+Image'; ?>" width="50" class="rounded" alt="Product">
                      <span><?php echo htmlspecialchars($item['name']); ?></span>
                    </div>
                  </td>
                  <td style="max-width: 100px;"><input type="number" class="form-control" value="<?php echo $item['qty']; ?>" min="1" disabled></td>
                  <td>₹<?php echo $item['price']; ?></td>
                  <td>₹<?php echo $item['total']; ?></td>
                  <td><a href="?remove=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4>Order Summary</h4>
          <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Subtotal
              <span>₹<?php echo $subtotal; ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              GST (18%)
              <span>₹<?php echo $gst; ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
              Total
              <span>₹<?php echo $total; ?></span>
            </li>
          </ul>
          <a href="checkout.php" class="btn btn-primary btn-lg w-100">Proceed to Checkout</a>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?> 