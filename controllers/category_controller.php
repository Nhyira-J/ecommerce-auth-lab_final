<?php
require_once '../classes/category_class.php';

class CategoryController {
    private $category;

    public function __construct($conn) {
        $this->category = new Category($conn);
    }

    // CREATE
    public function add_category_ctr($name, $user_id) {
        return $this->category->addCategory($name, $user_id);
    }

    // READ
    public function get_categories_ctr($user_id) {
        return $this->category->getCategoriesByUser($user_id);
    }

    // UPDATE
    public function update_category_ctr($id, $newName, $user_id) {
        return $this->category->updateCategory($id, $newName, $user_id);
    }

    // DELETE
    public function delete_category_ctr($id, $user_id) {
        return $this->category->deleteCategory($id, $user_id);
    }
}
