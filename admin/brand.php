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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management - ShopPN Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
        }
        .admin-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .admin-card h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .add-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .add-form input[type="text"],
        .add-form select {
            flex: 1;
            min-width: 200px;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .add-form input:focus,
        .add-form select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .add-form button {
            padding: 12px 28px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .add-form button:hover {
            background: #2980b9;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table thead {
            background: #f8f9fa;
        }
        .data-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        .action-btn {
            padding: 6px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            margin-right: 8px;
            transition: all 0.3s;
        }
        .edit-btn {
            background: #f39c12;
            color: white;
        }
        .edit-btn:hover {
            background: #e67e22;
        }
        .delete-btn {
            background: #e74c3c;
            color: white;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
    </style>
    <script defer src="../js/brand.js"></script>
</head>
<body>
    <div class="navbar">
        <h2>ShopPN Admin</h2>
        <div class="menu">
            <a href="../index.php">Home</a>
            <a href="category.php">Categories</a>
            <a href="brand.php">Brands</a>
            <a href="product.php">Products</a>
            <a href="../actions/logout_action.php" class="primary-btn small danger">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h1 style="color: #2c3e50; margin-bottom: 30px;"> Brand Management</h1>

        <div class="admin-card">
            <h2>Add New Brand</h2>
            <form id="addBrandForm" class="add-form">
                <input type="text" name="name" placeholder="Enter brand name..." required>
                <select name="cat_id" required>
                    <option value="">Select category...</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= htmlspecialchars($c['cat_id']) ?>">
                            <?= htmlspecialchars($c['cat_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">➕ Add Brand</button>
            </form>
        </div>

        <div class="admin-card">
            <h2>All Brands</h2>
            <table class="data-table" id="brandTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Brand Name</th>
                        <th>Category</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Filled by JS -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>