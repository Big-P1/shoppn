<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$formPage = BASE_URL . '/views/account/delete_account.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($formPage);
}

$password = $_POST['password'] ?? '';

if ($password === '' || ($_POST['confirm'] ?? '') !== '1') {
    $_SESSION['error'] = 'Enter your password and tick the confirmation box to delete your account.';
    redirect($formPage);
}

$controller = new CustomerController();
$result = $controller->deleteAccount((int) $_SESSION['customer_id'], $password);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect($formPage);
}

if (!empty($result['image'])) {
    @unlink(__DIR__ . '/../images/customers/' . basename($result['image']));
}

$_SESSION = [];
session_regenerate_id(true);
$_SESSION['success'] = 'Your account has been deleted.';

redirect(BASE_URL . '/index.php');
?>
