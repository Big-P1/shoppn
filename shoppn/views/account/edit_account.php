<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';

require_login();

$controller = new CustomerController();
$customer = $controller->getCustomerById($_SESSION['customer_id']);

if (!$customer) {
    redirect(BASE_URL . '/logout.php');
}

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'Ivory Coast', 'Togo', 'United States', 'United Kingdom', 'Canada', 'Other'];

if (!in_array($customer['customer_country'], $countries, true)) {
    array_unshift($countries, $customer['customer_country']);
}

$pageTitle = 'Edit Account';
include __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>Edit Account</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <form
        id="account-form"
        action="<?php echo BASE_URL; ?>/actions/update_account_action.php"
        method="POST"
        enctype="multipart/form-data"
    >
        <div class="field">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" maxlength="100" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
            <span class="field-error" data-error-for="name"></span>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($customer['customer_email']); ?>" disabled>
            <span class="field-hint">Your email is your login and cannot be changed here.</span>
        </div>

        <div class="field">
            <label for="country">Country</label>
            <select id="country" name="country" required>
                <option value="">Select country</option>
                <?php foreach ($countries as $country) { ?>
                    <option value="<?php echo htmlspecialchars($country); ?>" <?php echo $country === $customer['customer_country'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($country); ?>
                    </option>
                <?php } ?>
            </select>
            <span class="field-error" data-error-for="country"></span>
        </div>

        <div class="field">
            <label for="city">City</label>
            <input type="text" id="city" name="city" maxlength="30" value="<?php echo htmlspecialchars($customer['customer_city']); ?>" required>
            <span class="field-error" data-error-for="city"></span>
        </div>

        <div class="field">
            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" maxlength="15" value="<?php echo htmlspecialchars($customer['customer_contact']); ?>" required>
            <span class="field-error" data-error-for="contact"></span>
        </div>

        <div class="field">
            <label for="customer_image">Profile Image (optional)</label>
            <?php if (!empty($customer['customer_image'])) { ?>
                <img
                    class="avatar"
                    src="<?php echo BASE_URL; ?>/images/customers/<?php echo rawurlencode($customer['customer_image']); ?>"
                    alt="Current profile image"
                >
            <?php } ?>
            <input type="file" id="customer_image" name="customer_image" accept="image/jpeg,image/png,image/gif,image/webp">
            <span class="field-hint">Leave empty to keep your current image. JPG, PNG, GIF or WEBP, up to 2MB.</span>
            <span class="field-error" data-error-for="customer_image"></span>
        </div>

        <button type="submit" data-loading="Saving...">Save Changes</button>
        <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
