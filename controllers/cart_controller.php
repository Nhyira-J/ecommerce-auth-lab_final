<?php
require_once __DIR__ . '/../classes/cart_class.php';

class CartController {
    private $cart;

    public function __construct($conn) {
        $this->cart = new Cart($conn);
    }

    // Add to cart
    public function add_to_cart_ctr($p_id, $ip_add, $c_id, $qty) {
        return $this->cart->add_to_cart($p_id, $ip_add, $c_id, $qty);
    }

    // Get cart items
    public function get_cart_items_ctr($c_id, $ip_add) {
        return $this->cart->get_cart_items($c_id, $ip_add);
    }

    // Update cart quantity
    public function update_cart_qty_ctr($cart_id, $qty) {
        return $this->cart->update_cart_qty($cart_id, $qty);
    }

    // Remove from cart
    public function remove_from_cart_ctr($cart_id) {
        return $this->cart->remove_from_cart($cart_id);
    }

    // Empty cart
    public function empty_cart_ctr($c_id, $ip_add) {
        return $this->cart->empty_cart($c_id, $ip_add);
    }

    // Get cart count
    public function get_cart_count_ctr($c_id, $ip_add) {
        return $this->cart->get_cart_count($c_id, $ip_add);
    }
}
?>