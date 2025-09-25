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
    // Set session variables consistently with core.php
    $_SESSION['user_id']   = $customer['customer_id'];
    $_SESSION['name']      = $customer['customer_name'];
    $_SESSION['email']     = $customer['customer_email'];
    $_SESSION['role']      = $customer['user_role'];  // 👈 matches core.php

    echo json_encode([
        'status'  => 'success',
        'message' => 'Login successful'
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid email or password'
    ]);
}
