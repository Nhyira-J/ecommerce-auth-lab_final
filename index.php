<?php
session_start();

// Determine if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ShopPN - Home</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <!-- Navbar -->
  <div class="navbar">
    <h2>ShopPN</h2>
    <div class="menu">
      <?php if ($loggedIn): ?>
        <span>Welcome, <?= htmlspecialchars($name) ?></span>
        <a href="actions/logout_action.php" class="primary-btn">Logout</a>

        <?php if ($role == 1): ?>   <!-- 1 = admin -->
          <a href="view/admin/category.php" class="primary-btn">Categories</a>
          <a href="view/admin/products.php" class="primary-btn">Products</a>
        <?php endif; ?>

      <?php else: ?>
        <a href="view/register.php" class="primary-btn">Register</a>
        <a href="view/login.php" class="primary-btn">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="hero">
    <h1>Welcome to ShopPN!</h1>
    <p>Your one-stop shop for everything you need.</p>

    <?php if (!$loggedIn): ?>
      <div class="btn-group">
        <a href="view/register.php" class="register">Register</a>
        <a href="view/login.php" class="login">Login</a>
      </div>
    <?php else: ?>
      <p>
        <?php if ($role == 1): ?>
          Manage your store using the menu above.
        <?php else: ?>
          Start browsing our products!
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>
</body>
</html>
