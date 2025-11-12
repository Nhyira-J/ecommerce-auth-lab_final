<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'Guest';

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/product_controller.php';

$productCtrl = new ProductController($conn);

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: all_products.php');
    exit;
}

$product = $productCtrl->view_single_product_ctr($product_id);

if (!$product) {
    header('Location: all_products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_title']) ?> - ShopPN</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .product-detail-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .product-image-large { width: 100%; height: auto; border-radius: 8px; }
        .product-info h1 { font-size: 28px; margin-bottom: 10px; }
        .product-price-large { font-size: 32px; color: #e74c3c; font-weight: bold; margin: 20px 0; }
        .product-meta-info { background: #f5f5f5; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .product-meta-info p { margin: 8px 0; }
        .product-description { margin: 20px 0; line-height: 1.6; }
        .add-to-cart-large { background: #3498db; color: white; border: none; padding: 15px 40px; font-size: 18px; border-radius: 4px; cursor: pointer; margin-top: 20px; }
        .add-to-cart-large:hover { background: #2980b9; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        @media (max-width: 768px) {
            .product-detail { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>ShopPN</h2>
        <div class="menu">
            <a href="../index.php">Home</a>
            <a href="all_products.php">All Products</a>
            <a href="cart.php">🛒 Cart</a>
           

            <?php if ($loggedIn): ?>
            <span class="nav-welcome">Welcome, <?= htmlspecialchars($name) ?></span>
            
            <?php if ($role == 1): ?>
                <a href="../admin/category.php">Categories</a>
                <a href="../admin/brand.php">Brands</a>
                <a href="../admin/product.php">Products</a>
            <?php endif; ?>
            
            <a href="../actions/logout_action.php" class="primary-btn small danger">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a>
            <a href="login.php" class="primary-btn small">Login</a>
        <?php endif; ?>

        </div>
    </div>

    <div class="product-detail-container">
        <a href="all_products.php" class="back-link">← Back to All Products</a>

        <div class="product-detail">
            <div class="product-image-section">
                <?php if (!empty($product['product_image'])): ?>
                    <img src="../<?= htmlspecialchars($product['product_image']) ?>" 
                         alt="<?= htmlspecialchars($product['product_title']) ?>" 
                         class="product-image-large"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="product-image-large" style="background: #ddd; height: 400px; display: none; align-items: center; justify-content: center;">
                        Image Not Found
                    </div>
                <?php else: ?>
                    <div class="product-image-large" style="background: #ddd; height: 400px; display: flex; align-items: center; justify-content: center;">
                        No Image Available
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h1><?= htmlspecialchars($product['product_title']) ?></h1>
                
                <p class="product-price-large">GH₵<?= number_format($product['product_price'], 2) ?></p>

                <div class="product-meta-info">
                    <p><strong>Product ID:</strong> #<?= $product['product_id'] ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($product['cat_name'] ?? 'N/A') ?></p>
                    <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></p>
                    <?php if (!empty($product['product_keywords'])): ?>
                        <p><strong>Keywords:</strong> <?= htmlspecialchars($product['product_keywords']) ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($product['product_desc'])): ?>
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?= nl2br(htmlspecialchars($product['product_desc'])) ?></p>
                    </div>
                <?php endif; ?>

                <div style="display: flex; gap: 10px; align-items: center; margin-top: 20px;">
                    <label for="qty">Quantity:</label>
                    <input type="number" id="qty" value="1" min="1" style="width: 80px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <button class="add-to-cart-large" 
                        data-product-id="<?= $product['product_id'] ?>"
                        onclick="addToCart(this)">
                      Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
async function addToCart(button) {
    const productId = button.dataset.productId;
    const qty = parseInt(document.getElementById('qty').value);

    if (qty < 1) {
        alert('Quantity must be at least 1');
        return;
    }

    button.disabled = true;
    button.textContent = 'Adding...';

    try {
        const response = await fetch('../actions/add_to_cart_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&qty=${qty}`
        });

        const data = await response.json();

        if (data.status === 'success') {
            button.textContent = '✓ Added to Cart';
            alert(data.message + '\nGo to cart to checkout.');
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.disabled = false;
            }, 2000);
        } else {
            alert(data.message || 'Failed to add to cart');
            button.textContent = 'Add to Cart';
            button.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Network error occurred');
        button.textContent = 'Add to Cart';
        button.disabled = false;
    }
}
</script>
</body>
</html>