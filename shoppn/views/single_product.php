<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$proId = filter_var($_GET['pro_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($proId === false) {
    redirect(BASE_URL . '/index.php');
}

$controller = new ProductController();
$product = $controller->getProductById($proId);

if (!$product) {
    $_SESSION['error'] = 'That product could not be found.';
    redirect(BASE_URL . '/index.php');
}

$pageTitle = $product['product_title'];
include __DIR__ . '/layout/header.php';
?>

<div class="shop-layout">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="content">
        <?php include __DIR__ . '/layout/flash.php'; ?>

        <p><a href="<?php echo BASE_URL; ?>/index.php">&larr; Back to products</a></p>

        <div class="product-detail">
            <div class="detail-image">
                <?php if (!empty($product['product_image'])) { ?>
                    <img
                        src="<?php echo BASE_URL; ?>/images/products/<?php echo rawurlencode($product['product_image']); ?>"
                        alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                    >
                <?php } else { ?>
                    <span class="no-image">No image</span>
                <?php } ?>
            </div>

            <div class="detail-info">
                <h1><?php echo htmlspecialchars($product['product_title']); ?></h1>

                <ul class="meta-list">
                    <li><strong>Category:</strong>
                        <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo (int) $product['product_cat']; ?>"><?php echo htmlspecialchars($product['cat_name']); ?></a>
                    </li>
                    <li><strong>Brand:</strong>
                        <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo (int) $product['product_brand']; ?>"><?php echo htmlspecialchars($product['brand_name']); ?></a>
                    </li>
                </ul>

                <p class="price">GH&#8373; <?php echo number_format((float) $product['product_price'], 2); ?></p>

                <?php if (!empty($product['product_desc'])) { ?>
                    <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                <?php } ?>

                <?php if (!empty($product['product_keywords'])) { ?>
                    <p class="muted"><strong>Keywords:</strong> <?php echo htmlspecialchars($product['product_keywords']); ?></p>
                <?php } ?>

                <a class="btn" href="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php?add_cart=<?php echo (int) $product['product_id']; ?>">Add to Cart</a>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
