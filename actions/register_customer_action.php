<?php
// actions/register_customer_action.php

header('Content-Type: application/json');

// includes
require_once __DIR__ . '/../controllers/customer_controller.php';

// Collect POST data
$full_name       = trim($_POST['full_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = $_POST['password'] ?? '';
$country         = trim($_POST['country'] ?? '');
$city            = trim($_POST['city'] ?? '');
$contact_number  = trim($_POST['contact_number'] ?? '');

// Validation (server-side, in case JS misses anything)
if (!$full_name || !$email || !$password || !$country || !$city || !$contact_number) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Check email uniqueness
if (get_customer_by_email_ctr($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    exit;
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Build data array for model
$data = [
    'full_name'      => $full_name,
    'email'          => $email,
    'password'       => $hashed,
    'country'        => $country,
    'city'           => $city,
    'contact_number' => $contact_number,
    'image'          => null,   // default null
    'user_role'      => 2       // default customer
];

// Try to insert
$insertId = register_customer_ctr($data);

if ($insertId) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}
