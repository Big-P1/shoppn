<?php
require_once '../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $contact = $_POST['contact'];

    $controller = new CustomerController();

    if ($controller->register_customer($name, $email, $password, $country, $city, $contact)) {
        header("Location: ../views/login.php");
        exit();
    } else {
        header("Location: ../views/register.php?error=Registration failed");
        exit();
    }
}
?>