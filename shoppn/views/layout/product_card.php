<article class="product-card">
    <a class="card-image" href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo (int) $product['product_id']; ?>">
        <?php if (!empty($product['product_image'])) { ?>
            <img
                src="<?php echo BASE_URL; ?>/images/products/<?php echo rawurlencode($product['product_image']); ?>"
                alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                loading="lazy"
            >
        <?php } else { ?>
            <span class="no-image">No image</span>
        <?php } ?>
    </a>

    <div class="card-body">
        <h3><?php echo htmlspecialchars($product['product_title']); ?></h3>
        <p class="price">GH&#8373; <?php echo number_format((float) $product['product_price'], 2); ?></p>

        <div class="card-actions">
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo (int) $product['product_id']; ?>">Details</a>
            <a class="btn" href="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php?add_cart=<?php echo (int) $product['product_id']; ?>">Add to Cart</a>
        </div>
    </div>
</article>
