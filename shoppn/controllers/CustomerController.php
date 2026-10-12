<?php
require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController {

    private $customer;

    public function __construct() {
        $this->customer = new Customer();
    }

    public function register($data) {
        if ($this->customer->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        $id = $this->customer->addCustomer(
            $data['name'],
            $data['email'],
            $hash,
            $data['country'],
            $data['city'],
            $data['contact'],
            $data['image'] ?? null
        );

        if ($id === false) {
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }

        return [
            'success' => true,
            'customer' => $this->customer->getCustomerById($id)
        ];
    }

    public function login($email, $pass) {
        $row = $this->customer->login($email, $pass);

        if ($row === false) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        return ['success' => true, 'customer' => $row];
    }

    public function getCustomerById($id) {
        return $this->customer->getCustomerById($id);
    }
}
?>
