<?php
// controllers/customer_controller.php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../classes/customer_class.php';

function register_customer_ctr($data) {
    global $conn; // from db.php
    $customer = new Customer($conn);
    return $customer->addCustomer($data);
}

function get_customer_by_email_ctr($email) {
    global $conn;
    $customer = new Customer($conn);
    return $customer->getByEmail($email);
}
