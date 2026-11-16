<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$formPage = BASE_URL . '/views/account/edit_account.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($formPage);
}

$name = clean_input($_POST['name'] ?? '');
$country = clean_input($_POST['country'] ?? '');
$city = clean_input($_POST['city'] ?? '');
$contact = clean_input($_POST['contact'] ?? '');

$error = null;

if ($name === '' || $country === '' || $city === '' || $contact === '') {
    $error = 'Please fill in all required fields.';
} elseif (strlen($name) < 2 || strlen($name) > 100) {
    $error = 'Name must be between 2 and 100 characters.';
} elseif (strlen($country) > 30 || strlen($city) > 30) {
    $error = 'Country and city must be 30 characters or fewer.';
} elseif (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {
    $error = 'Contact number must be 7 to 15 digits (+, - and spaces allowed).';
}

if ($error !== null) {
    $_SESSION['error'] = $error;
    redirect($formPage);
}

$upload = upload_image('customer_image', __DIR__ . '/../images/customers');

if ($upload['status'] === 'error') {
    $_SESSION['error'] = $upload['error'];
    redirect($formPage);
}

$controller = new CustomerController();
$customerId = (int) $_SESSION['customer_id'];
$existing = $controller->getCustomerById($customerId);

$result = $controller->updateAccount($customerId, [
    'name' => $name,
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
    redirect($formPage);
}

if ($upload['filename'] !== null && $existing && !empty($existing['customer_image'])) {
    @unlink(__DIR__ . '/../images/customers/' . basename($existing['customer_image']));
}

$_SESSION['customer_name'] = $result['customer']['customer_name'];
$_SESSION['success'] = 'Your details have been updated.';

redirect(BASE_URL . '/views/account/my_account.php');
?>
