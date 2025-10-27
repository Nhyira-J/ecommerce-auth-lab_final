<?php
require_once __DIR__ . '/../classes/product_class.php';

class ProductController {
    private $product;

    public function __construct($conn) {
        $this->product = new Product($conn);
    }

    // View all products
    public function view_all_products_ctr($limit = null, $offset = 0) {
        return $this->product->view_all_products($limit, $offset);
    }

    // Search products
    public function search_products_ctr($query) {
        return $this->product->search_products($query);
    }

    // Filter by category
    public function filter_products_by_category_ctr($cat_id) {
        return $this->product->filter_products_by_category($cat_id);
    }

    // Filter by brand
    public function filter_products_by_brand_ctr($brand_id) {
        return $this->product->filter_products_by_brand($brand_id);
    }

    // View single product
    public function view_single_product_ctr($id) {
        return $this->product->view_single_product($id);
    }

    // Advanced search
    public function advanced_search_ctr($filters) {
        return $this->product->advanced_search($filters);
    }

    // Count products
    public function count_all_products_ctr() {
        return $this->product->count_all_products();
    }
}