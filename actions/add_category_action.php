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

$name = trim($_POST['name'] ?? '');

if (empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'Category name required']);
    exit;
}

$controller = new CategoryController($conn);
$success = $controller->add_category_ctr($name); // REMOVED $user_id parameter

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Category added']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Category could not be added (maybe duplicate?)']);
}