<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'Guest';

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

$c_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$ip_add = $_SERVER['REMOTE_ADDR'];

$cartCtrl = new CartController($conn);
$cart_items = $cartCtrl->get_cart_items_ctr($c_id, $ip_add);

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['product_price'] * $item['qty'];
}
$tax = $subtotal * 0.05; // 5% tax
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ShopPN</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .cart-container { max-width: 1200px; margin: 40px auto; padding: 20px; }
        .cart-table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 30px; }
        .cart-table th, .cart-table td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .cart-table th { background: #f5f5f5; font-weight: 600; }
        .cart-image { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; }
        .qty-input { width: 60px; padding: 5px; text-align: center; }
        .remove-btn { background: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .remove-btn:hover { background: #c0392b; }
        .cart-summary { background: white; padding: 25px; border-radius: 8px; max-width: 400px; margin-left: auto; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary-total { font-size: 20px; font-weight: bold; border-top: 2px solid #ddd; padding-top: 15px; margin-top: 15px; }
        .btn-group { display: flex; gap: 15px; margin-top: 20px; }
        .continue-btn { background: #95a5a6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; }
        .checkout-btn { background: #27ae60; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; }
        .empty-cart-btn { background: #e74c3c; color: white; border: none; padding: 12px 30px; border-radius: 4px; cursor: pointer; }
        .empty-cart { text-align: center; padding: 60px 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>ShopPN</h2>
        <div class="menu">
            <a href="../index.php">Home</a>
            <a href="all_products.php">All Products</a>
            <a href="cart.php">Cart (<span id="cart-count"><?= count($cart_items) ?></span>)</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../actions/logout_action.php" class="primary-btn small danger">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="cart-container">
        <h1>Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some products to get started!</p>
                <a href="all_products.php" class="continue-btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr data-cart-id="<?= $item['cart_id'] ?>">
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="../<?= htmlspecialchars($item['product_image']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_title']) ?>" 
                                             class="cart-image">
                                    <?php else: ?>
                                        <div class="cart-image" style="background: #ddd;"></div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($item['product_title']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td>GH₵<?= number_format($item['product_price'], 2) ?></td>
                            <td>
                                <input type="number" 
                                       class="qty-input" 
                                       value="<?= $item['qty'] ?>" 
                                       min="1" 
                                       data-cart-id="<?= $item['cart_id'] ?>"
                                       data-price="<?= $item['product_price'] ?>">
                            </td>
                            <td class="item-subtotal">GH₵<?= number_format($item['product_price'] * $item['qty'], 2) ?></td>
                            <td>
                                <button class="remove-btn" data-cart-id="<?= $item['cart_id'] ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="summary-subtotal">GH₵<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (5%):</span>
                    <span id="summary-tax">GH₵<?= number_format($tax, 2) ?></span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span id="summary-total">GH₵<?= number_format($total, 2) ?></span>
                </div>

                <div class="btn-group">
                    <a href="all_products.php" class="continue-btn">Continue Shopping</a>
                </div>
                <div class="btn-group">
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
                <div class="btn-group">
                    <button class="empty-cart-btn" id="empty-cart-btn">Empty Cart</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/cart.js"></script>
</body>
</html>