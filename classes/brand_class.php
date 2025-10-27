<?php
require_once __DIR__ . '/../functions/db.php';

class Brand {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // CREATE
    public function addBrand($name, $cat_id) {
        $sql = "INSERT INTO brands (brand_name, cat_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $name, $cat_id);
        return $stmt->execute();
    }

    // READ (all brands with category data)
    public function getAllBrands() {
        $sql = "SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name
                FROM brands b
                LEFT JOIN categories c ON b.cat_id = c.cat_id
                ORDER BY c.cat_name, b.brand_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    // UPDATE
    public function updateBrand($id, $newName, $cat_id) {
        $sql = "UPDATE brands SET brand_name = ?, cat_id = ? WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $newName, $cat_id, $id);
        return $stmt->execute();
    }

    // DELETE
    public function deleteBrand($id) {
        $sql = "DELETE FROM brands WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Optional: fetch brands by category
    public function getBrandsByCategory($cat_id) {
        $sql = "SELECT brand_id, brand_name FROM brands WHERE cat_id = ? ORDER BY brand_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}