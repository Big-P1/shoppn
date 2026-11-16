<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_login();

$cartController = new CartController();
$ip = get_ip();

$items = $cartController->getCartItems($ip);
$total = $cartController->getCartTotal($ip);

if (empty($items) || $total <= 0) {
    $_SESSION['error'] = 'Your cart is empty. Add something before paying.';
    redirect(BASE_URL . '/views/cart.php');
}

$reference = create_payment_reference();
$amountInPesewas = (int) round($total * 100);
$configured = paystack_configured();

$pageTitle = 'Payment';
include __DIR__ . '/layout/header.php';
?>

<div class="cart-page payment-page">
    <h2>Payment</h2>

    <?php include __DIR__ . '/layout/flash.php'; ?>

    <?php if (!$configured) { ?>
        <div class="alert alert-error">
            Paystack is not set up yet. Add your test keys to <code>core/paystack_cred.php</code> to enable payments.
        </div>
    <?php } ?>

    <div class="alert alert-error" id="payment-message" role="alert" hidden></div>

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
                        <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                        <td><?php echo (int) $item['qty']; ?></td>
                        <td>GH&#8373; <?php echo number_format((float) $item['product_price'], 2); ?></td>
                        <td>GH&#8373; <?php echo number_format((float) $item['line_total'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="3">Total</td>
                    <td>GH&#8373; <?php echo number_format($total, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="muted">You will pay securely through Paystack. Test card: 4084 0840 8408 4081, any future expiry, CVV 408.</p>

    <div class="cart-actions">
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/checkout.php">Back to Checkout</a>
        <button
            type="button"
            class="btn"
            id="pay-button"
            data-key="<?php echo htmlspecialchars(PAYSTACK_PUBLIC_KEY); ?>"
            data-email="<?php echo htmlspecialchars($_SESSION['customer_email'] ?? ''); ?>"
            data-amount="<?php echo $amountInPesewas; ?>"
            data-ref="<?php echo htmlspecialchars($reference); ?>"
            data-callback="<?php echo BASE_URL; ?>/actions/process_payment_action.php"
            <?php echo $configured ? '' : 'disabled'; ?>
        >Pay GH&#8373; <?php echo number_format($total, 2); ?></button>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="<?php echo BASE_URL; ?>/js/payment.js"></script>

<?php include __DIR__ . '/layout/footer.php'; ?>
