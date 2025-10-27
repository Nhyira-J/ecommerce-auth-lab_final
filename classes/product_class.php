<?php
require_once __DIR__ . '/../functions/db.php';

class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // View all products
    public function view_all_products($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                ORDER BY p.product_id DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $stmt = $this->conn->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search products by title
    public function search_products($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.product_title LIKE ?
                ORDER BY p.product_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Filter products by category
    public function filter_products_by_category($cat_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.product_cat = ?
                ORDER BY p.product_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Filter products by brand
    public function filter_products_by_brand($brand_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.product_brand = ?
                ORDER BY p.product_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // View single product
    public function view_single_product($id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.product_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // EXTRA CREDIT: Advanced search with multiple filters
    public function advanced_search($filters = []) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Search by keyword
        if (!empty($filters['search'])) {
            $sql .= " AND p.product_title LIKE ?";
            $params[] = "%{$filters['search']}%";
            $types .= "s";
        }
        
        // Filter by category
        if (!empty($filters['category'])) {
            $sql .= " AND p.product_cat = ?";
            $params[] = $filters['category'];
            $types .= "i";
        }
        
        // Filter by brand
        if (!empty($filters['brand'])) {
            $sql .= " AND p.product_brand = ?";
            $params[] = $filters['brand'];
            $types .= "i";
        }
        
        // Filter by max price
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.product_price <= ?";
            $params[] = $filters['max_price'];
            $types .= "d";
        }
        
        $sql .= " ORDER BY p.product_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count total products (for pagination)
    public function count_all_products() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}