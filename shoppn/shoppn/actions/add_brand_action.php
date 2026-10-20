<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

$page = BASE_URL . '/views/admin/brand.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($name === '') {
    $_SESSION['error'] = 'Please enter a brand name.';
    redirect($page);
}

if (strlen($name) < 2 || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name must be between 2 and 100 characters.';
    redirect($page);
}

$controller = new ProductController();

if ($controller->addBrand($name)) {
    $_SESSION['success'] = 'Brand added.';
} else {
    $_SESSION['error'] = 'Unable to add brand.';
}

redirect($page);
?>
