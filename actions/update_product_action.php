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

$id = intval($_POST['product_id'] ?? 0);
$cat = intval($_POST['product_cat'] ?? 0);
$brand = intval($_POST['product_brand'] ?? 0);
$title = trim($_POST['product_title'] ?? '');
$price = floatval($_POST['product_price'] ?? 0);
$desc = trim($_POST['product_desc'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');

if ($id <= 0 || $cat <= 0 || $brand <= 0 || $title === '' || $price <= 0) {
    echo json_encode(['status'=>'error','message'=>'All fields required']);
    exit;
}

$sql = "UPDATE products 
        SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_keywords = ? 
        WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisdssi", $cat, $brand, $title, $price, $desc, $keywords, $id);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Product updated']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to update product']);
}