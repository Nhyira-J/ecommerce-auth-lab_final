<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

$cart_id = intval($_POST['cart_id'] ?? 0);
$qty = intval($_POST['qty'] ?? 1);

if ($cart_id <= 0 || $qty <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$cartCtrl = new CartController($conn);

if ($cartCtrl->update_cart_qty_ctr($cart_id, $qty)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Quantity updated'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
}
?>