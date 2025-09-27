<?php
require_once '../functions/db.php';

class Category {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // CREATE
    public function addCategory($name, $user_id) {
        $query = "INSERT INTO categories (cat_name, user_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $name, $user_id);
        return $stmt->execute();
    }

    // READ
    public function getCategoriesByUser($user_id) {
        $query = "SELECT * FROM categories WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // UPDATE
    public function updateCategory($id, $name, $user_id) {
        $query = "UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $name, $id, $user_id);
        return $stmt->execute();
    }

    // DELETE
    public function deleteCategory($id, $user_id) {
        $query = "DELETE FROM categories WHERE cat_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }
}
