<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$cartController = new CartController();
$ip = get_ip();

$items = $cartController->getCartItems($ip);
$subtotal = $cartController->getCartTotal($ip);

$pageTitle = 'Shopping Cart';
include __DIR__ . '/layout/header.php';
?>

<div class="cart-page">
    <h2>Shopping Cart</h2>

    <?php include __DIR__ . '/layout/flash.php'; ?>

    <?php if (empty($items)) { ?>
        <div class="empty-state">
            Your cart is empty.
            <p><a class="btn" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a></p>
        </div>
    <?php } else { ?>
        <form id="remove-form" action="<?php echo BASE_URL; ?>/actions/remove_from_cart_action.php" method="POST"></form>
        <form id="update-form" action="<?php echo BASE_URL; ?>/actions/update_qty_action.php" method="POST"></form>

        <div class="cart-error alert alert-error" id="cart-error" role="alert" hidden></div>

        <div class="table-scroll">
            <table class="data-table cart-table">
                <thead>
                    <tr>
                        <th>Remove</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    form="remove-form"
                                    name="remove[]"
                                    value="<?php echo (int) $item['p_id']; ?>"
                                    aria-label="Remove <?php echo htmlspecialchars($item['product_title']); ?>"
                                >
                            </td>
                            <td>
                                <div class="cart-product">
                                    <?php if (!empty($item['product_image'])) { ?>
                                        <img
                                            class="thumb"
                                            src="<?php echo BASE_URL; ?>/images/products/<?php echo rawurlencode($item['product_image']); ?>"
                                            alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                        >
                                    <?php } ?>
                                    <a href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo (int) $item['p_id']; ?>">
                                        <?php echo htmlspecialchars($item['product_title']); ?>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" form="update-form" name="update[]" value="<?php echo (int) $item['p_id']; ?>">
                                <input
                                    class="qty-input"
                                    type="text"
                                    inputmode="numeric"
                                    form="update-form"
                                    name="qty[]"
                                    value="<?php echo (int) $item['qty']; ?>"
                                    maxlength="2"
                                    aria-label="Quantity for <?php echo htmlspecialchars($item['product_title']); ?>"
                                >
                            </td>
                            <td>GH&#8373; <?php echo number_format((float) $item['product_price'], 2); ?></td>
                            <td>GH&#8373; <?php echo number_format((float) $item['line_total'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="4">Subtotal</td>
                        <td>GH&#8373; <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="cart-actions">
            <button type="submit" form="remove-form" class="btn btn-outline" data-loading="Removing...">Remove from Cart</button>
            <button type="submit" form="update-form" class="btn btn-outline" data-loading="Updating...">Update Quantity</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
            <a class="btn" href="<?php echo BASE_URL; ?>/views/checkout.php">Checkout</a>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
