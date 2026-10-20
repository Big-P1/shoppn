<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

$page = BASE_URL . '/views/admin/product.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$cat = filter_var($_POST['cat_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand = filter_var($_POST['brand_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$title = trim(strip_tags($_POST['title'] ?? ''));
$priceInput = trim($_POST['price'] ?? '');
$desc = trim(strip_tags($_POST['desc'] ?? ''));
$keywords = trim(strip_tags($_POST['keywords'] ?? ''));

$controller = new ProductController();

$error = null;

if ($cat === false || !$controller->getCategoryById($cat)) {
    $error = 'Please select a valid category.';
} elseif ($brand === false || !$controller->getBrandById($brand)) {
    $error = 'Please select a valid brand.';
} elseif (strlen($title) < 2 || strlen($title) > 200) {
    $error = 'Title must be between 2 and 200 characters.';
} elseif (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $priceInput) || (float) $priceInput <= 0) {
    $error = 'Enter a price greater than 0 with up to 2 decimal places.';
} elseif (strlen($desc) > 500) {
    $error = 'Description must be 500 characters or fewer.';
} elseif (strlen($keywords) > 100) {
    $error = 'Keywords must be 100 characters or fewer.';
}

if ($error !== null) {
    $_SESSION['error'] = $error;
    redirect($page);
}

$upload = upload_image('product_image', __DIR__ . '/../images/products');

if ($upload['status'] === 'none') {
    $_SESSION['error'] = 'Please choose a product image.';
    redirect($page);
}

if ($upload['status'] === 'error') {
    $_SESSION['error'] = $upload['error'];
    redirect($page);
}

if ($controller->addProduct($cat, $brand, $title, (float) $priceInput, $desc, $upload['filename'], $keywords)) {
    $_SESSION['success'] = 'Product added.';
} else {
    @unlink(__DIR__ . '/../images/products/' . $upload['filename']);
    $_SESSION['error'] = 'Unable to add product.';
}

redirect($page);
?>
