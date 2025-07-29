<?php
// Admin sidebar partial for all admin pages
?>
<div class="col-lg-2 col-md-3 d-none d-lg-block sidebar">
  <ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo ' active'; ?>" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
    <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='products.php') echo ' active'; ?>" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
    <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='categories.php') echo ' active'; ?>" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
    <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='orders.php') echo ' active'; ?>" href="orders.php"><i class="bi bi-receipt me-2"></i>Orders</a></li>
    <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='settings.php') echo ' active'; ?>" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
    <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='customize.php') echo ' active'; ?>" href="customize.php"><i class="bi bi-sliders me-2"></i>Website Customization</a></li>
  </ul>
</div>
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="adminSidebarLabel">Admin Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body sidebar">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo ' active'; ?>" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
      <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='products.php') echo ' active'; ?>" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
      <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='categories.php') echo ' active'; ?>" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
      <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='orders.php') echo ' active'; ?>" href="orders.php"><i class="bi bi-receipt me-2"></i>Orders</a></li>
      <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='settings.php') echo ' active'; ?>" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
      <li class="nav-item"><a class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='customize.php') echo ' active'; ?>" href="customize.php"><i class="bi bi-sliders me-2"></i>Website Customization</a></li>
    </ul>
  </div>
</div> 