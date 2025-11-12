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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - ShopPN Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
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
            margin-bottom: 25px;
        }
        .product-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .form-group input[type="file"] {
            padding: 8px;
            border: 2px dashed #ddd;
            border-radius: 6px;
            cursor: pointer;
        }
        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        .save-btn {
            padding: 14px 32px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .save-btn:hover {
            background: #229954;
        }
        .cancel-btn {
            padding: 14px 32px;
            background: #95a5a6;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .cancel-btn:hover {
            background: #7f8c8d;
        }
        .msg {
            grid-column: 1 / -1;
            padding: 12px;
            border-radius: 6px;
            margin-top: 10px;
            display: none;
        }
        .msg.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        .msg.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .product-table thead {
            background: #f8f9fa;
        }
        .product-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }
        .product-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .product-table tbody tr:hover {
            background: #f8f9fa;
        }
        .product-image-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
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
        .price-badge {
            background: #27ae60;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }
    </style>
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
            <a href="../actions/logout_action.php" class="primary-btn small danger">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h1 style="color: #2c3e50; margin-bottom: 30px;"> Product Management</h1>

        <div class="admin-card">
            <h2>Add / Edit Product</h2>
            <form id="productForm" enctype="multipart/form-data" class="product-form">
                <input type="hidden" name="product_id" value="">
                
                <div class="form-group">
                    <label>Category *</label>
                    <select name="product_cat" id="product_cat" required>
                        <option value="">Select category...</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Brand *</label>
                    <select name="product_brand" id="product_brand" required>
                        <option value="">Select brand...</option>
                        <?php foreach($brands as $b): ?>
                            <option value="<?= $b['brand_id'] ?>" data-cat="<?= $b['cat_id'] ?>">
                                <?= htmlspecialchars($b['brand_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Product Title *</label>
                    <input type="text" name="product_title" id="product_title" placeholder="Enter product title..." required>
                </div>

                <div class="form-group">
                    <label>Price (GH₵) *</label>
                    <input type="number" step="0.01" name="product_price" id="product_price" placeholder="0.00" required>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="product_desc" id="product_desc" placeholder="Enter product description..."></textarea>
                </div>

                <div class="form-group">
                    <label>Keywords</label>
                    <input type="text" name="product_keywords" id="product_keywords" placeholder="e.g., shoes, sports, nike">
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" id="product_image" accept="image/*">
                    <small style="color: #7f8c8d; margin-top: 5px;">JPG, PNG, GIF, or WEBP (Max 5MB)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" id="saveProductBtn" class="save-btn"> Save Product</button>
                    <button type="button" class="cancel-btn" onclick="document.getElementById('productForm').reset(); document.querySelector('input[name=product_id]').value='';"> Cancel</button>
                </div>

                <div id="productMessage" class="msg"></div>
            </form>
        </div>

        <div class="admin-card">
            <h2>All Products</h2>
            <table class="product-table" id="productTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th style="width: 120px;">Price</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- filled by product.js -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>