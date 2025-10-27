<?php
require_once __DIR__ . '/../settings/core.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../view/login.php');
    exit;
}
require_once __DIR__ . '/../functions/db.php';

// Fetch all categories (shared across all admins)
$catStmt = $conn->prepare("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");
$catStmt->execute();
$catRes = $catStmt->get_result();
$categories = $catRes->fetch_all(MYSQLI_ASSOC);
$catStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Brands - Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <script defer src="../js/brand.js"></script>
</head>
<body>
  <div class="navbar">
    <h2>ShopPN Admin</h2>
    <div class="menu">
      <a href="../index.php">Home</a>
      <a href="category.php">Categories</a>
      <a href="brand.php">Brands</a>
      <a href="../actions/logout_action.php" class="primary-btn">Logout</a>
    </div>
  </div>

  <div class="container">
    <h1>Brand Management</h1>

    <form id="addBrandForm">
      <input type="text" name="name" placeholder="Brand name" required>
      <select name="cat_id" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= htmlspecialchars($c['cat_id']) ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Add Brand</button>
    </form>

    <hr>

    <table id="brandTable" border="1">
      <thead>
        <tr><th>ID</th><th>Name</th><th>Category</th><th>Actions</th></tr>
      </thead>
      <tbody><!-- filled by JS --></tbody>
    </table>
  </div>
</body>
</html>
