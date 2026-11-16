<?php
require_once __DIR__ . '/../../core/core.php';

require_login();

if (is_admin()) {
    $_SESSION['error'] = 'Administrator accounts cannot be deleted here.';
    redirect(BASE_URL . '/views/account/my_account.php');
}

$pageTitle = 'Delete Account';
include __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>Delete Account</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <div class="alert alert-error">
        This permanently removes your account and cannot be undone. Accounts that already have orders must be kept for our records and cannot be deleted.
    </div>

    <form id="delete-form" action="<?php echo BASE_URL; ?>/actions/delete_account_action.php" method="POST">
        <div class="field">
            <label for="password">Confirm with your password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            <span class="field-error" data-error-for="password"></span>
        </div>

        <div class="field">
            <label class="check-label">
                <input type="checkbox" name="confirm" value="1" required>
                I understand this cannot be undone.
            </label>
            <span class="field-error" data-error-for="confirm"></span>
        </div>

        <button type="submit" data-loading="Deleting...">Delete My Account</button>
        <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
