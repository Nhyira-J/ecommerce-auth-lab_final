<?php
require_once __DIR__ . '/../classes/brand_class.php';

class BrandController {
    private $brand;
    
    public function __construct($conn) { 
        $this->brand = new Brand($conn); 
    }

    public function add_brand_ctr($name, $cat_id) {
        return $this->brand->addBrand($name, $cat_id);
    }

    public function get_brands_ctr() {
        return $this->brand->getAllBrands();
    }

    public function update_brand_ctr($id, $name, $cat_id) {
        return $this->brand->updateBrand($id, $name, $cat_id);
    }

    public function delete_brand_ctr($id) {
        return $this->brand->deleteBrand($id);
    }

    public function get_brands_by_category_ctr($cat_id) {
        return $this->brand->getBrandsByCategory($cat_id);
    }
}