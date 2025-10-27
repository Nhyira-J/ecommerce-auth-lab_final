<?php
require_once __DIR__ . '/../classes/category_class.php';

class CategoryController {
    private $category;

    public function __construct($conn) {
        $this->category = new Category($conn);
    }

    // CREATE
    public function add_category_ctr($name) {
        return $this->category->addCategory($name);
    }

    // READ
    public function get_categories_ctr() {
        return $this->category->getAllCategories();
    }

    // UPDATE
    public function update_category_ctr($id, $newName) {
        return $this->category->updateCategory($id, $newName);
    }

    // DELETE
    public function delete_category_ctr($id) {
        return $this->category->deleteCategory($id);
    }
}