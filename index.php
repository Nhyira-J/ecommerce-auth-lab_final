<?php
session_start();
require_once __DIR__ . '/functions/db.php';
require_once __DIR__ . '/controllers/category_controller.php';
require_once __DIR__ . '/controllers/brand_controller.php';

// Determine if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'Guest';

// Fetch categories and brands for dropdowns
$categoryCtrl = new CategoryController($conn);
$brandCtrl = new BrandController($conn);
$categories = $categoryCtrl->get_categories_ctr();
$brands = $brandCtrl->get_brands_ctr();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopPN - Home</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .search-section {
      background: #f8f9fa;
      padding: 30px;
      margin: 20px auto;
      max-width: 800px;
      border-radius: 8px;
      text-align: center;
    }
    .search-form {
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 20px;
    }
    .search-form input[type="text"] {
      padding: 12px;
      width: 300px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    .search-form select {
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
      min-width: 150px;
    }
    .search-form button {
      padding: 12px 30px;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .search-form button:hover {
      background: #2980b9;
    }
    .quick-links {
      display: flex;
      gap: 15px;
      justify-content: center;
      margin-top: 20px;
    }
    .quick-links a {
      text-decoration: none;
      color: #3498db;
      font-size: 14px;
    }
    .quick-links a:hover {
      text-decoration: underline;
    }
    .features {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }
    .feature-card {
      background: white;
      padding: 30px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .feature-card h3 {
      color: #2c3e50;
      margin-bottom: 10px;
    }
    .feature-card p {
      color: #7f8c8d;
      line-height: 1.6;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <div class="navbar">
    <h2>ShopPN</h2>
    <div class="menu">
      <a href="index.php">Home</a>
      <a href="view/all_products.php">All Products</a>
      <a href="view/cart.php">🛒 Cart</a>
      
      <?php if ($loggedIn): ?>
        <span>Welcome, <?= htmlspecialchars($name) ?></span>
        
        <?php if ($role == 1): ?>   <!-- 1 = admin -->
          <a href="admin/category.php">Categories</a>
          <a href="admin/brand.php">Brands</a>
          <a href="admin/product.php">Products</a>
        <?php endif; ?>
        
        <a href="actions/logout_action.php" class="primary-btn">Logout</a>
      <?php else: ?>
        <a href="view/register.php">Register</a>
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
          Start browsing our products below!
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>

  <!-- Search Section -->
  <div class="search-section">
    <h2>Find What You're Looking For</h2>
    <form action="view/product_search_result.php" method="GET" class="search-form">
      <input 
        type="text" 
        name="q" 
        placeholder="Search for products..." 
        required
      >
      
      <select name="cat_id">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['cat_id'] ?>">
            <?= htmlspecialchars($cat['cat_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="brand_id">
        <option value="">All Brands</option>
        <?php foreach ($brands as $brand): ?>
          <option value="<?= $brand['brand_id'] ?>">
            <?= htmlspecialchars($brand['brand_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Search</button>
    </form>

    <div class="quick-links">
      <a href="view/all_products.php">Browse All Products</a>
      <?php foreach (array_slice($categories, 0, 3) as $cat): ?>
        <a href="view/all_products.php?cat_id=<?= $cat['cat_id'] ?>">
          <?= htmlspecialchars($cat['cat_name']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Features Section -->
  <div class="features">
    <div class="feature-card">
      <h3>🛍️ Wide Selection</h3>
      <p>Browse through our extensive collection of products across multiple categories and brands.</p>
    </div>
    
    <div class="feature-card">
      <h3>🔍 Easy Search</h3>
      <p>Find exactly what you need with our powerful search and filter options.</p>
    </div>
    
    <div class="feature-card">
      <h3>💳 Secure Shopping</h3>
      <p>Shop with confidence knowing your transactions are safe and secure.</p>
    </div>
    
    <div class="feature-card">
      <h3>🚚 Fast Delivery</h3>
      <p>Get your orders delivered quickly to your doorstep.</p>
    </div>
  </div>

</body>
</html>