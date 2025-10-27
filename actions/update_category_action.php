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

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');

if ($id <= 0 || empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'ID and name required']);
    exit;
}

$controller = new CategoryController($conn);
$success = $controller->update_category_ctr($id, $name); // REMOVED $user_id

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Category updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}