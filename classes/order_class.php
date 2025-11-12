<?php
require_once __DIR__ . '/../functions/db.php';

class Order {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create a new order
    public function create_order($customer_id, $invoice_no, $order_status = 'Pending') {
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_status, order_date) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $customer_id, $invoice_no, $order_status);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id; // Return the new order_id
        }
        return false;
    }

    // Add order details (products in the order)
    public function add_order_details($order_id, $product_id, $qty) {
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $order_id, $product_id, $qty);
        return $stmt->execute();
    }

    // Record payment
    public function record_payment($amt, $customer_id, $order_id, $currency = 'GHS') {
        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("diis", $amt, $customer_id, $order_id, $currency);
        return $stmt->execute();
    }

    // Get orders for a customer
    public function get_customer_orders($customer_id) {
        $sql = "SELECT o.*, p.amt, p.payment_date 
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = ?
                ORDER BY o.order_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get order details with products
    public function get_order_details($order_id) {
        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>