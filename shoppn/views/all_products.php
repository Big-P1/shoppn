<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();
$products = $controller->getAllProducts();

$pageTitle = 'All Products';
include __DIR__ . '/layout/header.php';
?>

<div class="shop-layout">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="content">
        <?php include __DIR__ . '/layout/flash.php'; ?>

        <h2>All Products</h2>

        <?php if (empty($products)) { ?>
            <div class="empty-state">No products found.</div>
        <?php } else { ?>
            <div class="product-grid">
                <?php foreach ($products as $product) { ?>
                    <?php include __DIR__ . '/layout/product_card.php'; ?>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
