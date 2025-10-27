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

$ctrl = new BrandController($conn);
$brands = $ctrl->get_brands_ctr(); // REMOVED $user_id
echo json_encode(['status'=>'success','data'=>$brands]);