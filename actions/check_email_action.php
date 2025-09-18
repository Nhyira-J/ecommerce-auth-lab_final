<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// include db connection
require_once("../functions/db.php");

if (!$conn) {
    echo json_encode(['available' => false, 'error' => 'DB connection failed']);
    exit;
}

if (!isset($_POST['email'])) {
    echo json_encode(['available' => false, 'error' => 'No email received']);
    exit;
}

$email = trim($_POST['email']);

$stmt = $conn->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['available' => false]);
} else {
    echo json_encode(['available' => true]);
}

$stmt->close();
$conn->close();

