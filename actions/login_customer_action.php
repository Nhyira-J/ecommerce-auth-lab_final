<?php
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../controllers/customer_controller.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

$controller = new CustomerController($conn);
$customer = $controller->login_customer_ctr(['email' => $email, 'password' => $password]);

if ($customer) {
    $_SESSION['user_id'] = $customer['customer_id'];
    $_SESSION['user_name'] = $customer['customer_name'];
    $_SESSION['user_email'] = $customer['customer_email'];
    $_SESSION['user_role'] = $customer['user_role'];

    echo json_encode(['status' => 'success', 'message' => 'Login successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
}
