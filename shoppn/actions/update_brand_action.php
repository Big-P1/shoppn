<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

$page = BASE_URL . '/views/admin/brand.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$id = filter_var($_POST['brand_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($id === false) {
    $_SESSION['error'] = 'Invalid brand.';
    redirect($page);
}

if ($name === '' || strlen($name) < 2 || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name must be between 2 and 100 characters.';
    redirect($page . '?edit_id=' . $id);
}

$controller = new ProductController();

if (!$controller->getBrandById($id)) {
    $_SESSION['error'] = 'Brand not found.';
    redirect($page);
}

if ($controller->updateBrand($id, $name)) {
    $_SESSION['success'] = 'Brand updated.';
} else {
    $_SESSION['error'] = 'Unable to update brand.';
}

redirect($page);
?>
