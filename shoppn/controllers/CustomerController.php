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

    public function updateAccount($id, $data) {
        $ok = $this->customer->updateCustomer(
            $id,
            $data['name'],
            $data['country'],
            $data['city'],
            $data['contact'],
            $data['image'] ?? null
        );

        if (!$ok) {
            return ['success' => false, 'error' => 'Your details could not be updated. Please try again.'];
        }

        return ['success' => true, 'customer' => $this->customer->getCustomerById($id)];
    }

    public function changePassword($id, $current, $new) {
        $customer = $this->customer->getCustomerById($id);

        if (!$customer || !password_verify($current, $customer['customer_pass'])) {
            return ['success' => false, 'error' => 'Your current password is incorrect.'];
        }

        $hash = password_hash($new, PASSWORD_BCRYPT);

        if (!$this->customer->updatePassword($id, $hash)) {
            return ['success' => false, 'error' => 'Your password could not be changed. Please try again.'];
        }

        return ['success' => true];
    }

    public function deleteAccount($id, $password) {
        $customer = $this->customer->getCustomerById($id);

        if (!$customer || !password_verify($password, $customer['customer_pass'])) {
            return ['success' => false, 'error' => 'Your password is incorrect.'];
        }

        if ((int) $customer['user_role'] === 1) {
            return ['success' => false, 'error' => 'Administrator accounts cannot be deleted here.'];
        }

        if (!$this->customer->deleteCustomer($id)) {
            return ['success' => false, 'error' => 'This account could not be deleted. Accounts with order history must be kept for our records.'];
        }

        return ['success' => true, 'image' => $customer['customer_image']];
    }
}
?>
