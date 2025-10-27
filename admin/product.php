<?php
require_once __DIR__ . '/../settings/core.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../view/login.php');
    exit;
}
require_once __DIR__ . '/../functions/db.php';

// fetch categories and brands for selects
$user_id = $_SESSION['user_id'];

// categories (shared across all admins)
$catStmt = $conn->prepare("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");
$catStmt->execute();
$catRes = $catStmt->get_result();
$categories = $catRes->fetch_all(MYSQLI_ASSOC);
$catStmt->close();

// brands (shared across all admins)
$brandStmt = $conn->prepare("SELECT brand_id, brand_name, cat_id FROM brands ORDER BY brand_name");
$brandStmt->execute();
$brandRes = $brandStmt->get_result();
$brands = $brandRes->fetch_all(MYSQLI_ASSOC);
$brandStmt->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Products - Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <script defer src="../js/product.js"></script>
</head>
<body>
  <div class="navbar">
    <h2>ShopPN Admin</h2>
    <div class="menu">
      <a href="../index.php">Home</a>
      <a href="category.php">Categories</a>
      <a href="brand.php">Brands</a>
      <a href="product.php">Products</a>
      <a href="../actions/logout_action.php" class="primary-btn">Logout</a>
    </div>
  </div>

  <div class="container">
    <h1>Product Management</h1>

    <form id="productForm" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="">
      <div class="form-group">
        <label>Category</label>
        <select name="product_cat" id="product_cat" required>
          <option value="">Select category</option>
          <?php foreach($categories as $c): ?>
            <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Brand</label>
        <select name="product_brand" id="product_brand" required>
          <option value="">Select brand</option>
          <?php foreach($brands as $b): ?>
            <option value="<?= $b['brand_id'] ?>" data-cat="<?= $b['cat_id'] ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Title</label>
        <input type="text" name="product_title" id="product_title" required>
      </div>

      <div class="form-group">
        <label>Price</label>
        <input type="number" step="0.01" name="product_price" id="product_price" required>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="product_desc" id="product_desc"></textarea>
      </div>

      <div class="form-group">
        <label>Keywords</label>
        <input type="text" name="product_keywords" id="product_keywords">
      </div>

      <div class="form-group">
        <label>Image</label>
        <input type="file" name="image" id="product_image" accept="image/*">
      </div>

      <button type="submit" id="saveProductBtn" class="primary-btn">Save Product</button>
      <div id="productMessage" class="msg"></div>
    </form>

    <hr>

    <h2>Existing Products</h2>
    <table id="productTable" border="1">
      <thead>
        <tr><th>ID</th><th>Image</th><th>Title</th><th>Brand</th><th>Category</th><th>Price</th><th>Actions</th></tr>
      </thead>
      <tbody><!-- filled by product.js --></tbody>
    </table>
  </div>
</body>
</html>
