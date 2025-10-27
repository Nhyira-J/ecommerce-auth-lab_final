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

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$cat_id = intval($_POST['cat_id'] ?? 0);

if ($id <= 0 || $cat_id <= 0 || $name === '') {
    echo json_encode(['status'=>'error','message'=>'Invalid input']);
    exit;
}

$ctrl = new BrandController($conn);
$ok = $ctrl->update_brand_ctr($id, $name, $cat_id); // REMOVED $user_id

if ($ok) echo json_encode(['status'=>'success','message'=>'Brand updated']);
else {
    $err = $conn->error ?? 'Unknown error';
    echo json_encode(['status'=>'error','message'=>'Update failed: '.$err]);
}