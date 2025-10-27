<?php
// actions/product_actions.php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

$productCtrl = new ProductController($conn);
$categoryCtrl = new CategoryController($conn);
$brandCtrl = new BrandController($conn);

// Get action from URL
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'view_all':
        $products = $productCtrl->view_all_products_ctr();
        echo json_encode(['status' => 'success', 'data' => $products]);
        break;

    case 'search':
        $query = $_GET['q'] ?? '';
        if (empty($query)) {
            echo json_encode(['status' => 'error', 'message' => 'Search query required']);
            exit;
        }
        $products = $productCtrl->search_products_ctr($query);
        echo json_encode(['status' => 'success', 'data' => $products]);
        break;

    case 'filter_category':
        $cat_id = intval($_GET['cat_id'] ?? 0);
        if ($cat_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Category ID required']);
            exit;
        }
        $products = $productCtrl->filter_products_by_category_ctr($cat_id);
        echo json_encode(['status' => 'success', 'data' => $products]);
        break;

    case 'filter_brand':
        $brand_id = intval($_GET['brand_id'] ?? 0);
        if ($brand_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Brand ID required']);
            exit;
        }
        $products = $productCtrl->filter_products_by_brand_ctr($brand_id);
        echo json_encode(['status' => 'success', 'data' => $products]);
        break;

    case 'get_single':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Product ID required']);
            exit;
        }
        $product = $productCtrl->view_single_product_ctr($id);
        echo json_encode(['status' => 'success', 'data' => $product]);
        break;

    case 'advanced_search':
        $filters = [
            'search' => $_GET['q'] ?? '',
            'category' => intval($_GET['cat_id'] ?? 0),
            'brand' => intval($_GET['brand_id'] ?? 0),
            'max_price' => floatval($_GET['max_price'] ?? 0)
        ];
        $products = $productCtrl->advanced_search_ctr($filters);
        echo json_encode(['status' => 'success', 'data' => $products]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}