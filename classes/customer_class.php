<?php
// classes/customer_class.php
class Customer {
    private $conn; // mysqli connection

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Add a new customer (returns inserted ID or false)
    public function addCustomer(array $data) {
        $sql = "INSERT INTO customer
            (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, customer_image, user_role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $this->conn->prepare($sql)) {
            $image = $data['image'] ?? null; // allow null
            $stmt->bind_param(
                'sssssssi',
                $data['full_name'],
                $data['email'],
                $data['password'],      // already hashed
                $data['country'],
                $data['city'],
                $data['contact_number'],
                $image,
                $data['user_role']
            );

            if ($stmt->execute()) {
                $insertId = $stmt->insert_id;
                $stmt->close();
                return $insertId;
            }
            $stmt->close();
        }
        return false;
    }

    // Check if email exists
    public function getByEmail(string $email) {
        $sql = "SELECT * FROM customer WHERE customer_email = ? LIMIT 1";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row ?: null;
        }
        return null;
    }
    public function login($email, $password) {
    $sql = "SELECT * FROM customer WHERE customer_email = ? LIMIT 1";
    if ($stmt = $this->conn->prepare($sql)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $customer = $res->fetch_assoc();
        $stmt->close();

        if ($customer && password_verify($password, $customer['customer_pass'])) {
            return $customer; // login successful
        }
    }
    return false; // login failed
}
}
