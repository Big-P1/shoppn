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

    private function fetchAll($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();

        return $rows;
    }

    private function generateInvoiceNo() {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $invoice = mt_rand(100000, 999999);

            $stmt = $this->conn->prepare("SELECT order_id FROM orders WHERE invoice_no = ?");
            $stmt->bind_param("i", $invoice);
            $stmt->execute();

            $taken = $stmt->get_result()->num_rows > 0;
            $stmt->close();

            if (!$taken) {
                return $invoice;
            }
        }

        throw new RuntimeException('Could not generate a unique invoice number.');
    }

    public function processPayment($customer_id, $ip, $reference, $amount) {
        try {
            $this->conn->begin_transaction();

            $stmt = $this->conn->prepare(
                "SELECT cart.p_id, cart.qty, products.product_price
                FROM cart
                JOIN products ON cart.p_id = products.product_id
                WHERE cart.ip_add = ?
                FOR UPDATE"
            );

            $stmt->bind_param("s", $ip);
            $lines = $this->fetchAll($stmt);

            if (empty($lines)) {
                $this->conn->rollback();
                return false;
            }

            $cartTotal = 0.0;

            foreach ($lines as $line) {
                $cartTotal += $line['qty'] * $line['product_price'];
            }

            if (abs($cartTotal - (float) $amount) > 0.009) {
                $this->conn->rollback();
                error_log('processPayment amount mismatch for reference ' . $reference);
                return false;
            }

            $invoice = $this->generateInvoiceNo();
            $today = date('Y-m-d');

            $stmt = $this->conn->prepare(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, ?, 'paid')"
            );

            $stmt->bind_param("iis", $customer_id, $invoice, $today);
            $stmt->execute();
            $stmt->close();

            $orderId = (int) $this->conn->insert_id;

            $stmt = $this->conn->prepare("INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)");

            foreach ($lines as $line) {
                $stmt->bind_param("iii", $orderId, $line['p_id'], $line['qty']);
                $stmt->execute();
            }

            $stmt->close();

            $paid = (float) $amount;
            $currency = 'GHS';

            $stmt = $this->conn->prepare(
                "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param("diiss", $paid, $customer_id, $orderId, $currency, $today);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->conn->prepare("DELETE FROM cart WHERE ip_add = ?");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();

            error_log('Payment recorded: order ' . $orderId . ', reference ' . $reference);

            return $orderId;
        } catch (Throwable $e) {
            try {
                $this->conn->rollback();
            } catch (Throwable $ignored) {
            }

            error_log('processPayment failed: ' . $e->getMessage());

            return false;
        }
    }

    public function getOrderById($order_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.order_id, o.customer_id, o.invoice_no, o.order_date, o.order_status,
                    c.customer_name, c.customer_email, c.customer_contact, c.customer_city, c.customer_country,
                    p.amt, p.currency, p.payment_date
            FROM orders o
            JOIN customer c ON o.customer_id = c.customer_id
            LEFT JOIN payment p ON p.order_id = o.order_id
            WHERE o.order_id = ?"
        );

        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "SELECT od.product_id, od.qty, pr.product_title, pr.product_price, pr.product_image,
                    (od.qty * pr.product_price) AS line_total
            FROM orderdetails od
            JOIN products pr ON od.product_id = pr.product_id
            WHERE od.order_id = ?
            ORDER BY pr.product_title ASC"
        );

        $stmt->bind_param("i", $order_id);

        return ['order' => $order, 'items' => $this->fetchAll($stmt)];
    }

    public function getOrdersByCustomer($customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status, p.amt, p.currency,
                    (SELECT COALESCE(SUM(od.qty), 0) FROM orderdetails od WHERE od.order_id = o.order_id) AS item_count
            FROM orders o
            LEFT JOIN payment p ON p.order_id = o.order_id
            WHERE o.customer_id = ?
            ORDER BY o.order_id DESC"
        );

        $stmt->bind_param("i", $customer_id);

        return $this->fetchAll($stmt);
    }

    public function getRecentlyPurchased($customer_id, $limit = 4) {
        $limit = max(1, (int) $limit);

        $stmt = $this->conn->prepare(
            "SELECT pr.*, MAX(o.order_id) AS last_order
            FROM orders o
            JOIN orderdetails od ON od.order_id = o.order_id
            JOIN products pr ON pr.product_id = od.product_id
            WHERE o.customer_id = ?
            GROUP BY pr.product_id
            ORDER BY last_order DESC
            LIMIT ?"
        );

        $stmt->bind_param("ii", $customer_id, $limit);

        return $this->fetchAll($stmt);
    }

    public function getRecommendedProducts($customer_id, $limit = 4) {
        $limit = max(1, (int) $limit);

        $stmt = $this->conn->prepare(
            "SELECT p.*,
                (CASE WHEN p.product_cat IN (
                    SELECT pr.product_cat FROM orders o
                    JOIN orderdetails od ON od.order_id = o.order_id
                    JOIN products pr ON pr.product_id = od.product_id
                    WHERE o.customer_id = ?
                ) THEN 2 ELSE 0 END
                + CASE WHEN p.product_brand IN (
                    SELECT pr.product_brand FROM orders o
                    JOIN orderdetails od ON od.order_id = o.order_id
                    JOIN products pr ON pr.product_id = od.product_id
                    WHERE o.customer_id = ?
                ) THEN 1 ELSE 0 END) AS score
            FROM products p
            WHERE p.product_id NOT IN (
                SELECT od.product_id FROM orders o
                JOIN orderdetails od ON od.order_id = o.order_id
                WHERE o.customer_id = ?
            )
            ORDER BY score DESC, RAND()
            LIMIT ?"
        );

        $stmt->bind_param("iiii", $customer_id, $customer_id, $customer_id, $limit);

        return $this->fetchAll($stmt);
    }
}
?>
