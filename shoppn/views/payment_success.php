<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_login();

$orderId = filter_var($_GET['order_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($orderId === false) {
    redirect(BASE_URL . '/views/account/my_orders.php');
}

$cartController = new CartController();
$result = $cartController->getOrderById($orderId);

if (!$result || ((int) $result['order']['customer_id'] !== (int) $_SESSION['customer_id'] && !is_admin())) {
    $_SESSION['error'] = 'That order could not be found.';
    redirect(BASE_URL . '/views/account/my_orders.php');
}

$order = $result['order'];
$items = $result['items'];
$recommended = $cartController->getRecommendedProducts((int) $order['customer_id'], 4);

$pageTitle = 'Order #' . $order['order_id'];
include __DIR__ . '/layout/header.php';
?>

<div class="cart-page invoice-page">
    <?php include __DIR__ . '/layout/flash.php'; ?>

    <article class="invoice">
        <header class="invoice-head">
            <div>
                <h2>Payment Receipt</h2>
                <p class="muted">Thank you for your order. Keep this page as your receipt.</p>
            </div>
            <span class="status-pill"><?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></span>
        </header>

        <div class="invoice-meta">
            <div>
                <span class="label">Invoice</span>
                <strong>INV-<?php echo (int) $order['invoice_no']; ?></strong>
            </div>
            <div>
                <span class="label">Order number</span>
                <strong>#<?php echo (int) $order['order_id']; ?></strong>
            </div>
            <div>
                <span class="label">Date</span>
                <strong><?php echo htmlspecialchars($order['order_date']); ?></strong>
            </div>
            <div>
                <span class="label">Billed to</span>
                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                <span><?php echo htmlspecialchars($order['customer_email']); ?></span>
            </div>
        </div>

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
                        <td colspan="3">Total paid</td>
                        <td>GH&#8373; <?php echo number_format((float) $order['amt'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </article>

    <div class="cart-actions no-print">
        <button type="button" class="btn btn-outline" id="print-receipt">Print Receipt</button>
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/account/my_orders.php">My Orders</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
    </div>

    <?php if (!empty($recommended)) { ?>
        <section class="recommend no-print">
            <h3>You might also like</h3>
            <div class="product-grid">
                <?php foreach ($recommended as $product) { ?>
                    <?php include __DIR__ . '/layout/product_card.php'; ?>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</div>

<script>
    document.getElementById('print-receipt').addEventListener('click', function () {
        window.print();
    });
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
