<?php
require_once '../functions/db.php';

class Category {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // CREATE
    public function addCategory($name, $user_id) {
        $query = "INSERT INTO categories (category_name, user_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $user_id]);
    }

    // READ
    public function getCategoriesByUser($user_id) {
        $query = "SELECT * FROM categories WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function updateCategory($id, $name, $user_id) {
        $query = "UPDATE categories SET category_name = ? 
                  WHERE category_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $id, $user_id]);
    }

    // DELETE
    public function deleteCategory($id, $user_id) {
        $query = "DELETE FROM categories WHERE category_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id, $user_id]);
    }
}
