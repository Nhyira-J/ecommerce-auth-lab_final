<?php
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/category_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$name = $_POST['name'] ?? '';
$user_id = $_SESSION['user_id'];

if (!$name) {
    echo json_encode(['status' => 'error', 'message' => 'Category name required']);
    exit;
}

$controller = new CategoryController($conn);
$success = $controller->add_category_ctr($name, $user_id);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Category added']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Category could not be added (maybe duplicate?)']);
}
