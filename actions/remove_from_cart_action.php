<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

$cart_id = intval($_POST['cart_id'] ?? 0);

if ($cart_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid cart item']);
    exit;
}

$cartCtrl = new CartController($conn);

if ($cartCtrl->remove_from_cart_ctr($cart_id)) {
    // Get updated cart count
    $c_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $cart_count = $cartCtrl->get_cart_count_ctr($c_id, $ip_add);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Item removed from cart',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
}
?>