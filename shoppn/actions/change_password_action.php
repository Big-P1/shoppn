<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$formPage = BASE_URL . '/views/account/change_pass.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($formPage);
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$error = null;

if ($current === '' || $new === '' || $confirm === '') {
    $error = 'Please fill in all password fields.';
} elseif (!password_is_strong($new)) {
    $error = 'Your new password does not meet the required rules.';
} elseif ($new !== $confirm) {
    $error = 'The new passwords do not match.';
} elseif ($new === $current) {
    $error = 'Your new password must be different from the current one.';
}

if ($error !== null) {
    $_SESSION['error'] = $error;
    redirect($formPage);
}

$controller = new CustomerController();
$result = $controller->changePassword((int) $_SESSION['customer_id'], $current, $new);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect($formPage);
}

session_regenerate_id(true);
$_SESSION['success'] = 'Your password has been changed.';

redirect(BASE_URL . '/views/account/my_account.php');
?>
