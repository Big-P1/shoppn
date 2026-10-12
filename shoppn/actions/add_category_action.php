<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

$page = BASE_URL . '/views/admin/category.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($name === '') {
    $_SESSION['error'] = 'Please enter a category name.';
    redirect($page);
}

if (strlen($name) < 2 || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name must be between 2 and 100 characters.';
    redirect($page);
}

$controller = new ProductController();

if ($controller->addCategory($name)) {
    $_SESSION['success'] = 'Category added.';
} else {
    $_SESSION['error'] = 'Unable to add category.';
}

redirect($page);
?>
