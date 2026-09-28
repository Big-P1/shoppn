<?php
require_once '../classes/CustomerClass.php';

class CustomerController {

    public function register_customer($name, $email, $password, $country, $city, $contact) {
        $customer = new Customer();

        if ($customer->email_exists($email)) {
            return false;
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        return $customer->add_customer($name, $email, $password, $country, $city, $contact);
    }
    public function login($email, $password) {
    $customer = new Customer();
    $user = $customer->getCustomerByEmail($email);

    if ($user && password_verify($password, $user['customer_pass'])) {
        return $user;
    }

    return false;
    }
}
?>