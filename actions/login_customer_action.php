<?php
session_start();
require_once("../controllers/customer_controller.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $customer = login_customer_ctr($email, $password);

    if ($customer) {
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['customer_name'];
        $_SESSION['user_role'] = $customer['user_role'];

        echo json_encode(["success" => true, "message" => "Login successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    }
}
?>
