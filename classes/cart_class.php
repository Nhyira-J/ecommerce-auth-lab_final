<?php
require_once __DIR__ . '/../functions/db.php';

class Cart {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Add product to cart (or update quantity if already exists)
    public function add_to_cart($p_id, $ip_add, $c_id, $qty) {
        // Check if product already in cart for this user/IP
        $check_sql = "SELECT cart_id, qty FROM cart WHERE p_id = ? AND (c_id = ? OR ip_add = ?)";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->bind_param("iis", $p_id, $c_id, $ip_add);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Product exists, update quantity
            $row = $result->fetch_assoc();
            $new_qty = $row['qty'] + $qty;
            $cart_id = $row['cart_id'];
            
            $update_sql = "UPDATE cart SET qty = ? WHERE cart_id = ?";
            $update_stmt = $this->conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_qty, $cart_id);
            return $update_stmt->execute();
        } else {
            // Product doesn't exist, insert new
            $insert_sql = "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)";
            $insert_stmt = $this->conn->prepare($insert_sql);
            $insert_stmt->bind_param("isii", $p_id, $ip_add, $c_id, $qty);
            return $insert_stmt->execute();
        }
    }

    // Get all cart items for a user (by customer_id or IP)
    public function get_cart_items($c_id, $ip_add) {
        $sql = "SELECT c.cart_id, c.p_id, c.qty, p.product_title, p.product_price, p.product_image, p.product_cat, p.product_brand
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id = ? OR c.ip_add = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $c_id, $ip_add);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update quantity of a cart item
    public function update_cart_qty($cart_id, $qty) {
        $sql = "UPDATE cart SET qty = ? WHERE cart_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $qty, $cart_id);
        return $stmt->execute();
    }

    // Remove item from cart
    public function remove_from_cart($cart_id) {
        $sql = "DELETE FROM cart WHERE cart_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }

    // Empty entire cart for a user
    public function empty_cart($c_id, $ip_add) {
        $sql = "DELETE FROM cart WHERE c_id = ? OR ip_add = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $c_id, $ip_add);
        return $stmt->execute();
    }

    // Get cart count
    public function get_cart_count($c_id, $ip_add) {
        $sql = "SELECT SUM(qty) as total FROM cart WHERE c_id = ? OR ip_add = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $c_id, $ip_add);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }
}
?>