<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

// Get product ID and quantity
$p_id = intval($_POST['product_id'] ?? 0);
$qty = intval($_POST['qty'] ?? 1);

// Get customer ID (if logged in) or IP address
$c_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$ip_add = $_SERVER['REMOTE_ADDR'];

if ($p_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

$cartCtrl = new CartController($conn);

if ($cartCtrl->add_to_cart_ctr($p_id, $ip_add, $c_id, $qty)) {
    // Get updated cart count
    $cart_count = $cartCtrl->get_cart_count_ctr($c_id, $ip_add);
    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to cart',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add to cart']);
}
?>