<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

$page = BASE_URL . '/views/admin/category.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$id = filter_var($_POST['cat_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($id === false) {
    $_SESSION['error'] = 'Invalid category.';
    redirect($page);
}

if ($name === '' || strlen($name) < 2 || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name must be between 2 and 100 characters.';
    redirect($page . '?edit_id=' . $id);
}

$controller = new ProductController();

if (!$controller->getCategoryById($id)) {
    $_SESSION['error'] = 'Category not found.';
    redirect($page);
}

if ($controller->updateCategory($id, $name)) {
    $_SESSION['success'] = 'Category updated.';
} else {
    $_SESSION['error'] = 'Unable to update category.';
}

redirect($page);
?>
