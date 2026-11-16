<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CartController.php';

require_login();

$cartController = new CartController();
$customerId = (int) $_SESSION['customer_id'];

$orders = $cartController->getOrdersByCustomer($customerId);
$recent = $cartController->getRecentlyPurchased($customerId, 4);
$recommended = $cartController->getRecommendedProducts($customerId, 4);

$pageTitle = 'My Orders';
include __DIR__ . '/../layout/header.php';
?>

<div class="cart-page">
    <h2>My Orders</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <?php if (empty($orders)) { ?>
        <div class="empty-state">
            You have not placed any orders yet.
            <p><a class="btn" href="<?php echo BASE_URL; ?>/index.php">Start Shopping</a></p>
        </div>
    <?php } else { ?>
        <div class="table-scroll">
            <table class="data-table cart-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td>#<?php echo (int) $order['order_id']; ?></td>
                            <td>INV-<?php echo (int) $order['invoice_no']; ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo (int) $order['item_count']; ?></td>
                            <td>GH&#8373; <?php echo number_format((float) $order['amt'], 2); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></td>
                            <td><a href="<?php echo BASE_URL; ?>/views/payment_success.php?order_id=<?php echo (int) $order['order_id']; ?>">View</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <?php if (!empty($recent)) { ?>
        <section class="recommend">
            <h3>Recently purchased</h3>
            <div class="product-grid">
                <?php foreach ($recent as $product) { ?>
                    <?php include __DIR__ . '/../layout/product_card.php'; ?>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <?php if (!empty($orders) && !empty($recommended)) { ?>
        <section class="recommend">
            <h3>Recommended for you</h3>
            <div class="product-grid">
                <?php foreach ($recommended as $product) { ?>
                    <?php include __DIR__ . '/../layout/product_card.php'; ?>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
