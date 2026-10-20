<?php
require_once __DIR__ . '/../core/core.php';

class Customer extends Database {

    public function emailExists($email) {
        $stmt = $this->conn->prepare(
            "SELECT customer_email FROM customer WHERE customer_email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    public function addCustomer($name, $email, $pass, $country, $city, $contact, $image = null) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO customer
                (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, customer_image)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param("sssssss", $name, $email, $pass, $country, $city, $contact, $image);

            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }

            $id = $this->conn->insert_id;
            $stmt->close();

            return $id;
        } catch (mysqli_sql_exception $e) {
            error_log('addCustomer failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getCustomerByEmail($email) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM customer WHERE customer_email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ? $row : false;
    }

    public function getCustomerById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM customer WHERE customer_id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ? $row : false;
    }

    public function login($email, $pass) {
        $row = $this->getCustomerByEmail($email);

        if ($row && password_verify($pass, $row['customer_pass'])) {
            return $row;
        }

        return false;
    }
}
?>
