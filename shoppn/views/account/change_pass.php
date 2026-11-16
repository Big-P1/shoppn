<?php
require_once __DIR__ . '/../../core/core.php';

require_login();

$pageTitle = 'Change Password';
include __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>Change Password</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <form
        id="password-form"
        data-password-field="new_password"
        action="<?php echo BASE_URL; ?>/actions/change_password_action.php"
        method="POST"
    >
        <div class="field">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            <span class="field-error" data-error-for="current_password"></span>
        </div>

        <div class="field">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" autocomplete="new-password" required>
            <span class="field-error" data-error-for="new_password"></span>
            <div class="password-rules">
                <p data-rule="length">&#9675; At least 8 characters</p>
                <p data-rule="uppercase">&#9675; One uppercase letter</p>
                <p data-rule="lowercase">&#9675; One lowercase letter</p>
                <p data-rule="number">&#9675; One number</p>
                <p data-rule="special">&#9675; One special character (!@#$% etc.)</p>
            </div>
        </div>

        <div class="field">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
            <span class="field-error" data-error-for="confirm_password"></span>
        </div>

        <button type="submit" data-loading="Updating...">Change Password</button>
        <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
