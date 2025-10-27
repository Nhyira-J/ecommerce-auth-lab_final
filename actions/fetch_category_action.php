<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/category_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$controller = new CategoryController($conn);
$categories = $controller->get_categories_ctr(); // REMOVED $user_id

if ($categories && count($categories) > 0) {
    echo json_encode(['status' => 'success', 'data' => $categories]);
} else {
    echo json_encode(['status' => 'success', 'data' => []]);
}