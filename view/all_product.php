<?php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

$productCtrl = new ProductController($conn);
$categoryCtrl = new CategoryController($conn);
$brandCtrl = new BrandController($conn);

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get filters from URL
$cat_filter = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
$brand_filter = isset($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;

// Fetch products based on filters
if ($cat_filter > 0) {
    $products = $productCtrl->filter_products_by_category_ctr($cat_filter);
} elseif ($brand_filter > 0) {
    $products = $productCtrl->filter_products_by_brand_ctr($brand_filter);
} else {
    $products = $productCtrl->view_all_products_ctr($limit, $offset);
}

// Get all categories and brands for filters
$categories = $categoryCtrl->get_categories_ctr();
$brands = $brandCtrl->get_brands_ctr();

// Calculate total pages
$totalProducts = $productCtrl->count_all_products_ctr();
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - ShopPN</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .products-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .filters { background: #f5f5f5; padding: 20px; margin-bottom: 30px; border-radius: 8px; }
        .filters select { padding: 8px; margin-right: 10px; border-radius: 4px; border: 1px solid #ddd; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .product-image { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        .product-title { font-size: 16px; font-weight: bold; margin: 10px 0; color: #333; }
        .product-price { font-size: 18px; color: #e74c3c; font-weight: bold; margin: 10px 0; }
        .product-meta { font-size: 12px; color: #666; margin: 5px 0; }
        .add-to-cart-btn { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .add-to-cart-btn:hover { background: #2980b9; }
        .pagination { text-align: center; margin-top: 30px; }
        .pagination a { padding: 8px 12px; margin: 0 5px; border: 1px solid #ddd; text-decoration: none; color: #333; }
        .pagination a.active { background: #3498db; color: white; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>ShopPN</h2>
        <div class="menu">
            <a href="../index.php">Home</a>
            <a href="all_products.php">All Products</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </div>
    </div>

    <div class="products-container">
        <h1>All Products</h1>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="">
                <select name="cat_id" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['cat_id'] ?>" <?= $cat_filter == $cat['cat_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['cat_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="brand_id" onchange="this.form.submit()">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['brand_id'] ?>" <?= $brand_filter == $brand['brand_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Filter</button>
                <?php if ($cat_filter || $brand_filter): ?>
                    <a href="all_products.php" style="margin-left: 10px;">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p>No products found.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['product_image'])): ?>
                            <img src="../<?= htmlspecialchars($product['product_image']) ?>" 
                                 alt="<?= htmlspecialchars($product['product_title']) ?>" 
                                 class="product-image"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="product-image" style="background: #ddd; display: none; align-items: center; justify-content: center;">
                                Image Not Found
                            </div>
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

                        <button class="add-to-cart-btn" onclick="alert('Add to cart functionality coming soon!')">
                            Add to Cart
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1 && !$cat_filter && !$brand_filter): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>