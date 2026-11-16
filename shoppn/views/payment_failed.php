<?php
require_once __DIR__ . '/../core/core.php';

require_login();

$pageTitle = 'Payment Failed';
include __DIR__ . '/layout/header.php';
?>

<div class="form-card">
    <h2>Payment Not Completed</h2>

    <?php include __DIR__ . '/layout/flash.php'; ?>

    <p>No order has been created from this attempt. Your cart is still saved, so you can try again whenever you are ready.</p>

    <div class="cart-actions">
        <a class="btn" href="<?php echo BASE_URL; ?>/views/payment.php">Try Again</a>
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/cart.php">Back to Cart</a>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
