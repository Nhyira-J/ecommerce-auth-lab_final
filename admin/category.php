<?php
require_once '../settings/core.php'; // has isLoggedIn() and isAdmin()

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../view/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Category Management</title>
  <link rel="stylesheet" href="../css/style.css">
  <script defer src="../js/category.js"></script>
</head>
<body>
  <div class="navbar">
    <h2>ShopPN Admin</h2>
    <div class="menu">
      <a href="../index.php">Home</a>
      <a href="../actions/logout_action.php" class="primary-btn">Logout</a>
    </div>
  </div>

  <div class="container">
    <h1>Manage Categories</h1>

    <!-- Add Category Form -->
    <form id="addCategoryForm">
      <input type="text" name="name" placeholder="Category Name" required>
      <button type="submit">Add Category</button>
    </form>

    <hr>

    <!-- Categories Table -->
    <h2>Your Categories</h2>
    <table border="1" cellpadding="10" id="categoryTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Filled dynamically by category.js -->
      </tbody>
    </table>
  </div>
</body>
</html>
