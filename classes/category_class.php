<?php
require_once __DIR__ . '/../functions/db.php';

class Category {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // CREATE
    public function addCategory($name) {
        $query = "INSERT INTO categories (cat_name) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    // READ - Get all categories (shared across all admins)
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY cat_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // UPDATE
    public function updateCategory($id, $name) {
        $query = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    // DELETE
    public function deleteCategory($id) {
        $query = "DELETE FROM categories WHERE cat_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}