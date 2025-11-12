<?php


session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to checkout']);
    exit;
}

$c_id = intval($_SESSION['user_id']);
$ip_add = $_SERVER['REMOTE_ADDR'];

$cartCtrl = new CartController($conn);
$orderCtrl = new OrderController($conn);

// Get cart items
$cart_items = $cartCtrl->get_cart_items_ctr($c_id, $ip_add);

if (empty($cart_items)) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
    exit;
}

// Calculate total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['product_price'] * $item['qty'];
}
$tax = $subtotal * 0.05; // 5% tax
$total = $subtotal + $tax;


// Generate unique invoice number
$invoice_no = rand(100000, 999999);

// Create order
$order_id = $orderCtrl->create_order_ctr($c_id, $invoice_no, 'Pending');

if (!$order_id) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create order']);
    exit;
}

// Add order details for each cart item
foreach ($cart_items as $item) {
    $orderCtrl->add_order_details_ctr($order_id, $item['p_id'], $item['qty']);
}

// Record payment
$orderCtrl->record_payment_ctr($total, $c_id, $order_id, 'GHS');

// Empty cart after successful checkout
$cartCtrl->empty_cart_ctr($c_id, $ip_add);

echo json_encode([
    'status' => 'success',
    'message' => 'Order placed successfully',
    'order_id' => $order_id,
    'invoice_no' => $invoice_no,
    'total' => number_format($total, 2)
]);
?>