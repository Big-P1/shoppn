<?php
require_once '../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $controller = new CustomerController();
    $user = $controller->login($email, $password);

    if ($user) {
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['customer_name'] = $user['customer_name'];
        $_SESSION['user_role'] = $user['user_role'];

        header("Location: ../index.php");
        exit();
    } else {
        header("Location: ../views/login.php?error=Invalid email or password");
        exit();
    }
}
?>