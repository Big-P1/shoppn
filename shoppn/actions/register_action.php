<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

$registerPage = BASE_URL . '/views/register.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($registerPage);
}

function clean($value) {
    return trim(strip_tags($value ?? ''));
}

$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$country = clean($_POST['country'] ?? '');
$city = clean($_POST['city'] ?? '');
$contact = clean($_POST['contact'] ?? '');

$error = null;

if ($name === '' || $email === '' || $password === '' || $country === '' || $city === '' || $contact === '') {
    $error = 'Please fill in all required fields.';
} elseif (strlen($name) < 2 || strlen($name) > 100) {
    $error = 'Name must be between 2 and 100 characters.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
} elseif (strlen($email) > 50) {
    $error = 'Email must be 50 characters or fewer.';
} elseif (strlen($country) > 30 || strlen($city) > 30) {
    $error = 'Country and city must be 30 characters or fewer.';
} elseif (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {
    $error = 'Contact number must be 7 to 15 digits (+, - and spaces allowed).';
} elseif (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[^A-Za-z0-9]/', $password)
) {
    $error = 'Password does not meet the required rules.';
}

if ($error !== null) {
    $_SESSION['error'] = $error;
    redirect($registerPage);
}

$upload = upload_image('customer_image', __DIR__ . '/../images/customers');

if ($upload['status'] === 'error') {
    $_SESSION['error'] = $upload['error'];
    redirect($registerPage);
}

$controller = new CustomerController();

$result = $controller->register([
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'country' => $country,
    'city' => $city,
    'contact' => $contact,
    'image' => $upload['filename']
]);

if (!$result['success']) {
    if ($upload['filename'] !== null) {
        @unlink(__DIR__ . '/../images/customers/' . $upload['filename']);
    }

    $_SESSION['error'] = $result['error'];
    redirect($registerPage);
}

$customer = $result['customer'];

session_regenerate_id(true);

$_SESSION['customer_id'] = (int) $customer['customer_id'];
$_SESSION['customer_name'] = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role'] = (int) $customer['user_role'];
$_SESSION['success'] = 'Welcome to Shoppn! Your account has been created.';

redirect(BASE_URL . '/views/account/my_account.php');
?>
