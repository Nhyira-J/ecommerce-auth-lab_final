<?php
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/category_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$controller = new CategoryController($conn);
$categories = $controller->get_categories_ctr($user_id);

echo json_encode(['status' => 'success', 'data' => $categories]);
