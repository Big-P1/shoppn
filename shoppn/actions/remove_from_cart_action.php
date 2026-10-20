<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$page = BASE_URL . '/views/cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$submitted = $_POST['remove'] ?? [];

if (!is_array($submitted)) {
    $submitted = [$submitted];
}

$ids = [];

foreach ($submitted as $value) {
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id === false) {
        $_SESSION['error'] = 'Invalid item selected.';
        redirect($page);
    }

    $ids[$id] = $id;
}

if (empty($ids)) {
    $_SESSION['error'] = 'Select at least one item to remove.';
    redirect($page);
}

$ip = get_ip();
$cart = new CartController();

$removed = 0;

foreach ($ids as $id) {
    if ($cart->removeFromCart($id, $ip)) {
        $removed++;
    }
}

if ($removed === 0) {
    $_SESSION['error'] = 'Unable to remove the selected items.';
} elseif ($removed === 1) {
    $_SESSION['success'] = 'Item removed from your cart.';
} else {
    $_SESSION['success'] = $removed . ' items removed from your cart.';
}

redirect($page);
?>
