<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/brand_controller.php';
require_once '../settings/core.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$cat_id = intval($_POST['cat_id'] ?? 0);

if ($name === '' || $cat_id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Name and category required']);
    exit;
}

$ctrl = new BrandController($conn);
$ok = $ctrl->add_brand_ctr($name, $cat_id); // REMOVED $user_id
echo json_encode($ok ? ['status'=>'success','message'=>'Brand added'] : ['status'=>'error','message'=>'Could not add brand (duplicate?)']);