<?php
// view/partials/navbar.php
// safe include: expects core.php to have isLoggedIn(), isAdmin(), getUserId(), getUserName() etc.
require_once __DIR__ . '/../../settings/core.php'; // adjust path if needed

$loggedIn = isLoggedIn();
$isAdmin = isAdmin();
$userName = $_SESSION['user_name'] ?? ($_SESSION['name'] ?? 'Guest');
?>

<nav class="navbar">
  <div class="nav-left">
    <a class="brand" href="/mvc_skeleton_template/index.php">ShopPN</a>
  </div>

  <div class="nav-center">
    <form id="navSearchForm" action="/mvc_skeleton_template/view/all_products.php" method="get" class="nav-search">
      <input type="hidden" name="action" value="search">
      <input id="navSearchInput" name="q" type="search" placeholder="Search products..." aria-label="Search products">
      <button type="submit" class="primary-btn small">Search</button>
    </form>
  </div>

  <div class="nav-right">
    <?php if ($loggedIn): ?>
      <span class="nav-welcome">Hi, <?= htmlspecialchars($userName) ?></span>

      <?php if ($isAdmin): ?>
        <a href="/mvc_skeleton_template/view/admin/category.php" class="primary-btn small">Categories</a>
        <a href="/mvc_skeleton_template/view/admin/brand.php" class="primary-btn small">Brands</a>
        <a href="/mvc_skeleton_template/view/admin/product.php" class="primary-btn small">Products</a>
      <?php else: ?>
        <a href="/mvc_skeleton_template/view/all_products.php" class="primary-btn small">All Products</a>
        <!-- optional: cart link -->
        <a href="/mvc_skeleton_template/view/cart.php" class="primary-btn small">Cart</a>
      <?php endif; ?>

      <a href="/mvc_skeleton_template/actions/logout_action.php" class="primary-btn small danger">Logout</a>
    <?php else: ?>
      <a href="/mvc_skeleton_template/view/all_products.php" class="primary-btn small">All Products</a>
      <a href="/mvc_skeleton_template/view/register.php" class="primary-btn small">Register</a>
      <a href="/mvc_skeleton_template/view/login.php" class="primary-btn small">Login</a>
    <?php endif; ?>
  </div>
</nav>
