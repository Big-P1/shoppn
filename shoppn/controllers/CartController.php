<?php
require_once __DIR__ . '/../classes/CartClass.php';

class CartController {

    private $cart;

    public function __construct() {
        $this->cart = new Cart();
    }

    public function isInCart($product_id, $ip) {
        return $this->cart->isInCart($product_id, $ip);
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        return $this->cart->addToCart($product_id, $qty, $ip, $customer_id);
    }

    public function getCartItems($ip) {
        return $this->cart->getCartItems($ip);
    }

    public function getCartCount($ip) {
        return $this->cart->getCartCount($ip);
    }

    public function getCartTotal($ip) {
        return $this->cart->getCartTotal($ip);
    }

    public function removeFromCart($product_id, $ip) {
        return $this->cart->removeFromCart($product_id, $ip);
    }

    public function updateQty($product_id, $new_qty, $ip) {
        return $this->cart->updateQty($product_id, $new_qty, $ip);
    }

    public function processPayment($customer_id, $ip, $reference, $amount) {
        return $this->cart->processPayment($customer_id, $ip, $reference, $amount);
    }

    public function getOrderById($order_id) {
        return $this->cart->getOrderById($order_id);
    }

    public function getOrdersByCustomer($customer_id) {
        return $this->cart->getOrdersByCustomer($customer_id);
    }

    public function getRecentlyPurchased($customer_id, $limit = 4) {
        return $this->cart->getRecentlyPurchased($customer_id, $limit);
    }

    public function getRecommendedProducts($customer_id, $limit = 4) {
        return $this->cart->getRecommendedProducts($customer_id, $limit);
    }
}
?>
