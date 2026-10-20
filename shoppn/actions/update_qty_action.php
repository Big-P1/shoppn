<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$page = BASE_URL . '/views/cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$productIds = $_POST['update'] ?? [];
$quantities = $_POST['qty'] ?? [];

if (!is_array($productIds) || !is_array($quantities) || count($productIds) !== count($quantities) || empty($productIds)) {
    $_SESSION['error'] = 'Nothing to update.';
    redirect($page);
}

$updates = [];

foreach (array_values($productIds) as $index => $rawId) {
    $id = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $qty = filter_var(trim((string) array_values($quantities)[$index]), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);

    if ($id === false || $qty === false) {
        $_SESSION['error'] = 'Quantity must be a whole number between 1 and 99.';
        redirect($page);
    }

    $updates[$id] = $qty;
}

$ip = get_ip();
$cart = new CartController();

$failed = 0;

foreach ($updates as $id => $qty) {
    if (!$cart->updateQty($id, $qty, $ip)) {
        $failed++;
    }
}

if ($failed > 0) {
    $_SESSION['error'] = 'Some quantities could not be updated.';
} else {
    $_SESSION['success'] = 'Cart updated.';
}

redirect($page);
?>
