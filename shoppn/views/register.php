<?php
require_once __DIR__ . '/../core/core.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/views/account/my_account.php');
}

$pageTitle = 'Create Account';
include __DIR__ . '/layout/header.php';

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'Ivory Coast', 'Togo', 'United States', 'United Kingdom', 'Canada', 'Other'];
?>

<div class="form-card">
    <h2>Create Your Account</h2>

    <?php include __DIR__ . '/layout/flash.php'; ?>

    <form
        id="register-form"
        action="<?php echo BASE_URL; ?>/actions/register_action.php"
        method="POST"
        enctype="multipart/form-data"
    >
        <div class="field">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" maxlength="100" required>
            <span class="field-error" data-error-for="name"></span>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="50" required>
            <span class="field-error" data-error-for="email"></span>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <span class="field-error" data-error-for="password"></span>
            <div class="password-rules">
                <p data-rule="length">&#9675; At least 8 characters</p>
                <p data-rule="uppercase">&#9675; One uppercase letter</p>
                <p data-rule="lowercase">&#9675; One lowercase letter</p>
                <p data-rule="number">&#9675; One number</p>
                <p data-rule="special">&#9675; One special character (!@#$% etc.)</p>
            </div>
        </div>

        <div class="field">
            <label for="country">Country</label>
            <select id="country" name="country" required>
                <option value="">Select country</option>
                <?php foreach ($countries as $country) { ?>
                    <option value="<?php echo htmlspecialchars($country); ?>"><?php echo htmlspecialchars($country); ?></option>
                <?php } ?>
            </select>
            <span class="field-error" data-error-for="country"></span>
        </div>

        <div class="field">
            <label for="city">City</label>
            <input type="text" id="city" name="city" maxlength="30" required>
            <span class="field-error" data-error-for="city"></span>
        </div>

        <div class="field">
            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" maxlength="15" required>
            <span class="field-error" data-error-for="contact"></span>
        </div>

        <div class="field">
            <label for="customer_image">Profile Image (optional)</label>
            <input type="file" id="customer_image" name="customer_image" accept="image/jpeg,image/png,image/gif,image/webp">
            <span class="field-hint">JPG, PNG, GIF or WEBP, up to 2MB.</span>
            <span class="field-error" data-error-for="customer_image"></span>
        </div>

        <button type="submit" data-loading="Creating account...">Create Account</button>
    </form>

    <p class="form-footer">Already have an account? <a href="<?php echo BASE_URL; ?>/views/login.php">Login</a></p>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
