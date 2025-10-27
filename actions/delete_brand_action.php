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

if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Brand id required']);
    exit;
}

$ctrl = new BrandController($conn);
$ok = $ctrl->delete_brand_ctr($id); // REMOVED $user_id
echo json_encode($ok ? ['status'=>'success','message'=>'Brand deleted'] : ['status'=>'error','message'=>'Delete failed']);