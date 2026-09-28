<?php
require_once '../core/core.php';

class Customer extends Database {

    public function add_customer($name, $email, $password, $country, $city, $contact) {
        $stmt = $this->conn->prepare(
            "INSERT INTO customer 
            (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("ssssss", $name, $email, $password, $country, $city, $contact);

        return $stmt->execute();
    }

    public function email_exists($email) {
        $stmt = $this->conn->prepare(
            "SELECT customer_id FROM customer WHERE customer_email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
    public function getCustomerByEmail($email) {
    $stmt = $this->conn->prepare(
        "SELECT * FROM customer WHERE customer_email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc();
   }
}
?>