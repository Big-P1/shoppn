<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

$query = trim(strip_tags($_GET['user_query'] ?? ''));
$query = substr($query, 0, 100);

$products = $query === '' ? [] : $controller->searchProducts($query);

$pageTitle = 'Search Results';
include __DIR__ . '/layout/header.php';
?>

<div class="shop-layout">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="content">
        <?php include __DIR__ . '/layout/flash.php'; ?>

        <?php if ($query === '') { ?>
            <h2>Search Results</h2>
            <div class="empty-state">Type something in the search box to find products.</div>
        <?php } else { ?>
            <h2>Results for &ldquo;<?php echo htmlspecialchars($query); ?>&rdquo;</h2>

            <?php if (empty($products)) { ?>
                <div class="empty-state">No products found.</div>
            <?php } else { ?>
                <p class="muted"><?php echo count($products); ?> product<?php echo count($products) === 1 ? '' : 's'; ?> found</p>

                <div class="product-grid">
                    <?php foreach ($products as $product) { ?>
                        <?php include __DIR__ . '/layout/product_card.php'; ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </section>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
