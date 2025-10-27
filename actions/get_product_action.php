<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/product_controller.php';
require_once '../settings/core.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$id = intval($_GET['product_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Product ID required']);
    exit;
}

$ctrl = new ProductController($conn);
$product = $ctrl->view_single_product_ctr($id);

if ($product) {
    echo json_encode(['status'=>'success','data'=>$product]);
} else {
    echo json_encode(['status'=>'error','message'=>'Product not found']);
}