<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

$page = BASE_URL . '/views/admin/product.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$id = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['error'] = 'Invalid product.';
    redirect($page);
}

$editPage = $page . '?edit_id=' . $id;

$controller = new ProductController();
$existing = $controller->getProductById($id);

if (!$existing) {
    $_SESSION['error'] = 'Product not found.';
    redirect($page);
}

$cat = filter_var($_POST['cat_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand = filter_var($_POST['brand_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$title = trim(strip_tags($_POST['title'] ?? ''));
$priceInput = trim($_POST['price'] ?? '');
$desc = trim(strip_tags($_POST['desc'] ?? ''));
$keywords = trim(strip_tags($_POST['keywords'] ?? ''));

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
    redirect($editPage);
}

$filename = $existing['product_image'];
$hidden = $_POST['existing_image'] ?? '';

if ($hidden !== '' && $hidden === $existing['product_image']) {
    $filename = $hidden;
}

$upload = upload_image('product_image', __DIR__ . '/../images/products');

if ($upload['status'] === 'error') {
    $_SESSION['error'] = $upload['error'];
    redirect($editPage);
}

if ($upload['status'] === 'ok') {
    $filename = $upload['filename'];
}

if ($controller->updateProduct($id, $cat, $brand, $title, (float) $priceInput, $desc, $filename, $keywords)) {
    if ($upload['status'] === 'ok' && !empty($existing['product_image'])) {
        @unlink(__DIR__ . '/../images/products/' . basename($existing['product_image']));
    }

    $_SESSION['success'] = 'Product updated.';
    redirect($page);
}

if ($upload['status'] === 'ok') {
    @unlink(__DIR__ . '/../images/products/' . $upload['filename']);
}

$_SESSION['error'] = 'Unable to update product.';
redirect($editPage);
?>
