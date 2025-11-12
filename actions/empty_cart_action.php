<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

$c_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$ip_add = $_SERVER['REMOTE_ADDR'];

$cartCtrl = new CartController($conn);

if ($cartCtrl->empty_cart_ctr($c_id, $ip_add)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Cart emptied successfully',
        'cart_count' => 0
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to empty cart']);
}
?>