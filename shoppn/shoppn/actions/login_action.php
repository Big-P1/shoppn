<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

$loginPage = BASE_URL . '/views/login.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($loginPage);
}

$email = trim(strip_tags($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
    $_SESSION['error'] = 'Invalid email or password';
    redirect($loginPage);
}

$controller = new CustomerController();
$result = $controller->login($email, $password);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect($loginPage);
}

$customer = $result['customer'];

session_regenerate_id(true);

$_SESSION['customer_id'] = (int) $customer['customer_id'];
$_SESSION['customer_name'] = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role'] = (int) $customer['user_role'];

redirect(BASE_URL . '/index.php');
?>
