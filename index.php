<?php
session_start();

// Determine if user is logged in
$loggedIn = isset($_SESSION['user_id']);
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
        <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="actions/logout_action.php" class="primary-btn">Logout</a>
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
      <p>Start browsing our products!</p>
    <?php endif; ?>
  </div>

</body>
</html>
