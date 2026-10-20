<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';

require_login();

$controller = new CustomerController();
$customer = $controller->getCustomerById($_SESSION['customer_id']);

if (!$customer) {
    redirect(BASE_URL . '/logout.php');
}

$pageTitle = 'My Account';
include __DIR__ . '/../layout/header.php';
?>

<div class="form-card account-card">
    <h2>My Account</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <?php if (!empty($customer['customer_image'])) { ?>
        <img
            class="avatar"
            src="<?php echo BASE_URL; ?>/images/customers/<?php echo rawurlencode($customer['customer_image']); ?>"
            alt="Profile image"
        >
    <?php } ?>

    <dl>
        <dt>Name</dt>
        <dd><?php echo htmlspecialchars($customer['customer_name']); ?></dd>

        <dt>Email</dt>
        <dd><?php echo htmlspecialchars($customer['customer_email']); ?></dd>

        <dt>Country</dt>
        <dd><?php echo htmlspecialchars($customer['customer_country']); ?></dd>

        <dt>City</dt>
        <dd><?php echo htmlspecialchars($customer['customer_city']); ?></dd>

        <dt>Contact</dt>
        <dd><?php echo htmlspecialchars($customer['customer_contact']); ?></dd>

        <dt>Account type</dt>
        <dd><?php echo is_admin() ? 'Administrator' : 'Customer'; ?></dd>
    </dl>

    <p class="form-footer"><a href="<?php echo BASE_URL; ?>/index.php">Continue shopping</a></p>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
