<?php


session_start();
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$c_id = intval($_SESSION['user_id']);
$ip_add = $_SERVER['REMOTE_ADDR'];

$cartCtrl = new CartController($conn);
$cart_items = $cartCtrl->get_cart_items_ctr($c_id, $ip_add);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['product_price'] * $item['qty'];
}
$tax = $subtotal * 0.05;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ShopPN</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .checkout-container { max-width: 800px; margin: 40px auto; padding: 20px; }
        .checkout-summary { background: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        .checkout-item { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee; }
        .checkout-total { font-size: 24px; font-weight: bold; margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd; }
        .payment-btn { background: #27ae60; color: white; border: none; padding: 15px 40px; font-size: 18px; border-radius: 4px; cursor: pointer; width: 100%; }
        .payment-btn:hover { background: #229954; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 10% auto; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px; text-align: center; }
        .modal-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 25px; }
        .modal-btn { padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .confirm-btn { background: #27ae60; color: white; }
        .cancel-btn { background: #95a5a6; color: white; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>ShopPN</h2>
        <div class="menu">
            <a href="../index.php">Home</a>
            <a href="all_products.php">All Products</a>
            <a href="cart.php">Cart</a>
            <a href="../actions/logout_action.php" class="primary-btn small danger">Logout</a>
        </div>
    </div>

    <div class="checkout-container">
        <a href="cart.php" class="back-link">← Back to Cart</a>
        <h1>Checkout</h1>

        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <?php foreach ($cart_items as $item): ?>
                <div class="checkout-item">
                    <div>
                        <strong><?= htmlspecialchars($item['product_title']) ?></strong><br>
                        <small>Quantity: <?= $item['qty'] ?></small>
                    </div>
                    <div>GH₵<?= number_format($item['product_price'] * $item['qty'], 2) ?></div>
                </div>
            <?php endforeach; ?>

            <div style="margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal:</span>
                    <span>GH₵<?= number_format($subtotal, 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Tax (5%):</span>
                    <span>GH₵<?= number_format($tax, 2) ?></span>
                </div>
                <div class="checkout-total" style="display: flex; justify-content: space-between;">
                    <span>Total:</span>
                    <span>GH₵<?= number_format($total, 2) ?></span>
                </div>
            </div>

            <button class="payment-btn" id="payment-btn">Simulate Payment</button>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <h2>Confirm Payment</h2>
            <p>Total Amount: <strong>GH₵<?= number_format($total, 2) ?></strong></p>
            <p>Have you completed the payment?</p>
            <div class="modal-buttons">
                <button class="modal-btn confirm-btn" id="confirm-payment">Yes, I've Paid</button>
                <button class="modal-btn cancel-btn" id="cancel-payment">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="modal-content">
            <h2>✅ Payment Successful!</h2>
            <p>Your order has been placed successfully.</p>
            <p>Order Reference: <strong id="order-reference"></strong></p>
            <p>Total Paid: <strong id="total-paid"></strong></p>
            <button class="modal-btn confirm-btn" onclick="window.location.href='all_products.php'">Continue Shopping</button>
        </div>
    </div>

    <script src="../js/checkout.js"></script>
</body>
</html>