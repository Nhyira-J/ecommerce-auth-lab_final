<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'Guest';

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

$productCtrl = new ProductController($conn);
$categoryCtrl = new CategoryController($conn);
$brandCtrl = new BrandController($conn);

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_filter = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
$brand_filter = isset($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;

// Perform search
if (!empty($search_query) || $cat_filter > 0 || $brand_filter > 0) {
    $filters = [
        'search' => $search_query,
        'category' => $cat_filter,
        'brand' => $brand_filter
    ];
    $products = $productCtrl->advanced_search_ctr($filters);
} else {
    $products = [];
}

// Get all categories and brands for filters
$categories = $categoryCtrl->get_categories_ctr();
$brands = $brandCtrl->get_brands_ctr();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - ShopPN</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .search-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .search-header { margin-bottom: 20px; }
        .search-filters { background: #f5f5f5; padding: 20px; margin-bottom: 30px; border-radius: 8px; }
        .search-filters input, .search-filters select { padding: 8px; margin-right: 10px; border-radius: 4px; border: 1px solid #ddd; }
        .search-filters button { padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .product-image { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        .product-title { font-size: 16px; font-weight: bold; margin: 10px 0; color: #333; }
        .product-price { font-size: 18px; color: #e74c3c; font-weight: bold; margin: 10px 0; }
        .product-meta { font-size: 12px; color: #666; margin: 5px 0; }
        .add-to-cart-btn { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .add-to-cart-btn:hover { background: #2980b9; }
        .no-results { text-align: center; padding: 40px; color: #666; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>ShopPN</h2>
        <div class="menu">
            <a href="../index.php">Home</a>
            <a href="all_products.php">All Products</a>
            <a href="view/cart.php">🛒 Cart</a>
           

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

    <div class="search-container">
        <div class="search-header">
            <h1>Search Results</h1>
            <?php if (!empty($search_query)): ?>
                <p>Showing results for: <strong>"<?= htmlspecialchars($search_query) ?>"</strong></p>
            <?php endif; ?>
            <p>Found <?= count($products) ?> product(s)</p>
        </div>

        <!-- Search & Filter Form -->
        <div class="search-filters">
            <form method="GET" action="">
                <input type="text" name="q" placeholder="Search products..." value="<?= htmlspecialchars($search_query) ?>" style="width: 300px;">
                
                <select name="cat_id">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['cat_id'] ?>" <?= $cat_filter == $cat['cat_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['cat_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="brand_id">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['brand_id'] ?>" <?= $brand_filter == $brand['brand_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Search</button>
                <a href="all_products.php" style="margin-left: 10px;">Clear All</a>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <div class="no-results">
                    <h3>No products found</h3>
                    <p>Try adjusting your search or filters</p>
                    <a href="all_products.php">View All Products</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['product_image'])): ?>
                            <img src="../images/product/<?= htmlspecialchars($product['product_image']) ?>" 
                                 alt="<?= htmlspecialchars($product['product_title']) ?>" 
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image" style="background: #ddd; display: flex; align-items: center; justify-content: center;">
                                No Image
                            </div>
                        <?php endif; ?>

                        <h3 class="product-title">
                            <a href="single_product.php?id=<?= $product['product_id'] ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($product['product_title']) ?>
                            </a>
                        </h3>

                        <p class="product-price">GH₵<?= number_format($product['product_price'], 2) ?></p>
                        
                        <p class="product-meta">
                            Category: <?= htmlspecialchars($product['cat_name'] ?? 'N/A') ?><br>
                            Brand: <?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?>
                        </p>

                        <button class="add-to-cart-btn" 
                           data-product-id="<?= $product['product_id'] ?>"
                           onclick="addToCart(this)">
                          Add to Cart
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
async function addToCart(button) {
    const productId = button.dataset.productId;
    button.disabled = true;
    button.textContent = 'Adding...';

    try {
        const response = await fetch('../actions/add_to_cart_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&qty=1`
        });

        const data = await response.json();

        if (data.status === 'success') {
            button.textContent = '✓ Added';
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.disabled = false;
            }, 2000);
            alert(data.message);
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