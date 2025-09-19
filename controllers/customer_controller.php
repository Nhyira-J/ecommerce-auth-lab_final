<?php
// controllers/customer_controller.php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../classes/customer_class.php';

class CustomerController {
    private $customer;

    public function __construct($conn) {
        $this->customer = new Customer($conn);
    }

    // Register a new customer
    public function register_customer_ctr(array $data) {
        return $this->customer->addCustomer($data);
    }

    // Get a customer by email
    public function get_customer_by_email_ctr(string $email) {
        return $this->customer->getByEmail($email);
    }

    // Login customer
    public function login_customer_ctr(array $data) {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (!$email || !$password) return false;

        return $this->customer->login($email, $password);
    }
}
