<?php
session_start();

require_once 'db_cred.php';
require_once 'db_class.php';

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: ../views/login.php");
        exit();
    }
}

function require_admin() {
    if (!is_admin()) {
        header("Location: ../views/login.php");
        exit();
    }
}
?>