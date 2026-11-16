<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$cartController = new CartController();
$customerController = new CustomerController();
$ip = get_ip();

$items = $cartController->getCartItems($ip);
$subtotal = $cartController->getCartTotal($ip);
$customer = $customerController->getCustomerById($_SESSION['customer_id']);

if (empty($items)) {
    $_SESSION['error'] = 'Your cart is empty. Add something before checking out.';
    redirect(BASE_URL . '/views/cart.php');
}

if (!$customer) {
    redirect(BASE_URL . '/logout.php');
}

$pageTitle = 'Checkout';
include __DIR__ . '/layout/header.php';
?>

<div class="cart-page checkout-page">
    <h2>Checkout</h2>

    <?php include __DIR__ . '/layout/flash.php'; ?>

    <div class="checkout-grid">
        <section class="panel">
            <h3>Order summary</h3>

            <div class="table-scroll">
                <table class="data-table cart-table">
                    <thead>
                        <tr>
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
                                    <div class="cart-product">
                                        <?php if (!empty($item['product_image'])) { ?>
                                            <img
                                                class="thumb"
                                                src="<?php echo BASE_URL; ?>/images/products/<?php echo rawurlencode($item['product_image']); ?>"
                                                alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                            >
                                        <?php } ?>
                                        <span><?php echo htmlspecialchars($item['product_title']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo (int) $item['qty']; ?></td>
                                <td>GH&#8373; <?php echo number_format((float) $item['product_price'], 2); ?></td>
                                <td>GH&#8373; <?php echo number_format((float) $item['line_total'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="subtotal-row">
                            <td colspan="3">Total to pay</td>
                            <td>GH&#8373; <?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <aside class="panel">
            <h3>Your details</h3>
            <dl class="detail-list">
                <dt>Name</dt>
                <dd><?php echo htmlspecialchars($customer['customer_name']); ?></dd>

                <dt>Email</dt>
                <dd><?php echo htmlspecialchars($customer['customer_email']); ?></dd>

                <dt>Contact</dt>
                <dd><?php echo htmlspecialchars($customer['customer_contact']); ?></dd>

                <dt>Location</dt>
                <dd><?php echo htmlspecialchars($customer['customer_city'] . ', ' . $customer['customer_country']); ?></dd>
            </dl>
            <p class="muted"><a href="<?php echo BASE_URL; ?>/views/account/edit_account.php">Edit your details</a></p>
        </aside>
    </div>

    <div class="cart-actions">
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/cart.php">Back to Cart</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/views/payment.php">Proceed to Payment</a>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
