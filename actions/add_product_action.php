<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../settings/core.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$cat = intval($_POST['product_cat'] ?? 0);
$brand = intval($_POST['product_brand'] ?? 0);
$title = trim($_POST['product_title'] ?? '');
$price = floatval($_POST['product_price'] ?? 0);
$desc = trim($_POST['product_desc'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');

if ($cat <= 0 || $brand <= 0 || $title === '' || $price <= 0) {
    echo json_encode(['status'=>'error','message'=>'All fields required']);
    exit;
}

// Insert product
$sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_keywords) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisdss", $cat, $brand, $title, $price, $desc, $keywords);

if ($stmt->execute()) {
    $product_id = $conn->insert_id;
    echo json_encode(['status'=>'success','message'=>'Product added','product_id'=>$product_id]);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to add product']);
}