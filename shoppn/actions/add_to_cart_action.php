<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$fallback = BASE_URL . '/index.php';

$productId = filter_var($_GET['add_cart'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($productId === false) {
    $_SESSION['error'] = 'That product could not be added to your cart.';
    redirect_back($fallback);
}

$products = new ProductController();

if (!$products->getProductById($productId)) {
    $_SESSION['error'] = 'That product could not be found.';
    redirect_back($fallback);
}

$ip = get_ip();
$customerId = is_logged_in() ? (int) $_SESSION['customer_id'] : null;

$cart = new CartController();

if ($cart->isInCart($productId, $ip)) {
    $_SESSION['info'] = 'This item is already in your cart. Go to cart to update quantity.';
    redirect_back($fallback);
}

if ($cart->addToCart($productId, 1, $ip, $customerId)) {
    $_SESSION['success'] = 'Item added to your cart.';
} else {
    $_SESSION['error'] = 'Unable to add that item to your cart.';
}

redirect_back($fallback);
?>
