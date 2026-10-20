<?php
require_once __DIR__ . '/../core/core.php';

class Cart extends Database {

    private function run($stmt) {
        try {
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log('Cart query failed: ' . $e->getMessage());
            return false;
        }
    }

    public function isInCart($product_id, $ip) {
        $stmt = $this->conn->prepare("SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param("is", $product_id, $ip);
        $stmt->execute();

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $product_id, $ip, $customer_id, $qty);

        return $this->run($stmt);
    }

    public function getCartItems($ip) {
        $stmt = $this->conn->prepare(
            "SELECT cart.*, products.product_title, products.product_price, products.product_image,
                    (cart.qty * products.product_price) AS line_total
            FROM cart
            JOIN products ON cart.p_id = products.product_id
            WHERE cart.ip_add = ?
            ORDER BY products.product_title ASC"
        );

        $stmt->bind_param("s", $ip);
        $stmt->execute();

        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();

        return $rows;
    }

    public function getCartCount($ip) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int) $row['total'];
    }

    public function getCartTotal($ip) {
        $stmt = $this->conn->prepare(
            "SELECT SUM(cart.qty * products.product_price) AS total
            FROM cart
            JOIN products ON cart.p_id = products.product_id
            WHERE cart.ip_add = ?"
        );

        $stmt->bind_param("s", $ip);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (float) ($row['total'] ?? 0);
    }

    public function removeFromCart($product_id, $ip) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param("is", $product_id, $ip);

        return $this->run($stmt);
    }

    public function updateQty($product_id, $new_qty, $ip) {
        if ($new_qty < 1) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param("iis", $new_qty, $product_id, $ip);

        return $this->run($stmt);
    }
}
?>
