<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CartController.php';

$controller = new ProductController();

$catId = filter_var($_GET['cat'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brandId = filter_var($_GET['brand'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$heading = 'Featured Products';
$isFiltered = false;

if ($catId !== false) {
    $products = $controller->getProductsByCategory($catId);
    $category = $controller->getCategoryById($catId);
    $heading = $category ? $category['cat_name'] : 'Category';
    $isFiltered = true;
} elseif ($brandId !== false) {
    $products = $controller->getProductsByBrand($brandId);
    $brand = $controller->getBrandById($brandId);
    $heading = $brand ? $brand['brand_name'] : 'Brand';
    $isFiltered = true;
} else {
    $products = $controller->getFeaturedProducts(6);
}

$recent = [];
$recommended = [];

if (!$isFiltered && is_logged_in()) {
    $orderController = new CartController();
    $recent = $orderController->getRecentlyPurchased((int) $_SESSION['customer_id'], 4);
    $recommended = $orderController->getRecommendedProducts((int) $_SESSION['customer_id'], 4);
}

$pageTitle = $isFiltered ? $heading : 'Home';
include __DIR__ . '/layout/header.php';
?>

<div class="shop-layout">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="content">
        <?php include __DIR__ . '/layout/flash.php'; ?>

        <?php if (!$isFiltered) { ?>
            <div class="hero">
                <h1>Discover Ghanaian Fashion</h1>
                <p>Explore fashion and style from Ghanaian brands.</p>
            </div>
        <?php } ?>

        <h2><?php echo htmlspecialchars($heading); ?></h2>

        <?php if ($isFiltered) { ?>
            <p><a href="<?php echo BASE_URL; ?>/index.php">&larr; Back to featured products</a></p>
        <?php } ?>

        <?php if (empty($products)) { ?>
            <div class="empty-state">No products found.</div>
        <?php } else { ?>
            <div class="product-grid">
                <?php foreach ($products as $product) { ?>
                    <?php include __DIR__ . '/layout/product_card.php'; ?>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty($recent)) { ?>
            <section class="recommend">
                <h2>Recently purchased</h2>
                <div class="product-grid">
                    <?php foreach ($recent as $product) { ?>
                        <?php include __DIR__ . '/layout/product_card.php'; ?>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>

        <?php if (!empty($recent) && !empty($recommended)) { ?>
            <section class="recommend">
                <h2>Recommended for you</h2>
                <div class="product-grid">
                    <?php foreach ($recommended as $product) { ?>
                        <?php include __DIR__ . '/layout/product_card.php'; ?>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>
    </section>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
