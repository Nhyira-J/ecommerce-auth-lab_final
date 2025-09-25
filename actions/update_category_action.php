<?php
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/category_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$id = $_POST['id'] ?? '';
$newName = $_POST['name'] ?? '';
$user_id = $_SESSION['user_id'];

if (!$id || !$newName) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$controller = new CategoryController($conn);
$success = $controller->update_category_ctr($id, $newName, $user_id);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Category updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
