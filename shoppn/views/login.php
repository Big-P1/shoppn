<?php
require_once __DIR__ . '/../core/core.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/index.php');
}

$pageTitle = 'Login';
include __DIR__ . '/layout/header.php';
?>

<div class="form-card">
    <h2>Customer Login</h2>

    <?php include __DIR__ . '/layout/flash.php'; ?>

    <form id="login-form" action="<?php echo BASE_URL; ?>/actions/login_action.php" method="POST">
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="50" required>
            <span class="field-error" data-error-for="email"></span>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <span class="field-error" data-error-for="password"></span>
        </div>

        <button type="submit" data-loading="Logging in...">Login</button>
    </form>

    <p class="form-footer">New here? <a href="<?php echo BASE_URL; ?>/views/register.php">Create an account</a></p>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
