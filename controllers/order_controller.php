<?php
require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;

    public function __construct($conn) {
        $this->order = new Order($conn);
    }

    // Create order
    public function create_order_ctr($customer_id, $invoice_no, $order_status = 'Pending') {
        return $this->order->create_order($customer_id, $invoice_no, $order_status);
    }

    // Add order details
    public function add_order_details_ctr($order_id, $product_id, $qty) {
        return $this->order->add_order_details($order_id, $product_id, $qty);
    }

    // Record payment
    public function record_payment_ctr($amt, $customer_id, $order_id, $currency = 'GHS') {
        return $this->order->record_payment($amt, $customer_id, $order_id, $currency);
    }

    // Get customer orders
    public function get_customer_orders_ctr($customer_id) {
        return $this->order->get_customer_orders($customer_id);
    }

    // Get order details
    public function get_order_details_ctr($order_id) {
        return $this->order->get_order_details($order_id);
    }
}
?>