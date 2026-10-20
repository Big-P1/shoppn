<?php
require_once __DIR__ . '/../core/core.php';

class Product extends Database {

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

    private function fetchOne($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ? $row : false;
    }

    private function run($stmt) {
        try {
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log('Product query failed: ' . $e->getMessage());
            return false;
        }
    }

    public function addBrand($name) {
        $stmt = $this->conn->prepare("INSERT INTO brands (brand_name) VALUES (?)");
        $stmt->bind_param("s", $name);

        return $this->run($stmt);
    }

    public function getAllBrands() {
        $stmt = $this->conn->prepare("SELECT * FROM brands ORDER BY brand_name ASC");

        return $this->fetchAll($stmt);
    }

    public function getBrandById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $id);

        return $this->fetchOne($stmt);
    }

    public function updateBrand($id, $name) {
        $stmt = $this->conn->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ?");
        $stmt->bind_param("si", $name, $id);

        return $this->run($stmt);
    }

    public function addCategory($name) {
        $stmt = $this->conn->prepare("INSERT INTO categories (cat_name) VALUES (?)");
        $stmt->bind_param("s", $name);

        return $this->run($stmt);
    }

    public function getAllCategories() {
        $stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY cat_name ASC");

        return $this->fetchAll($stmt);
    }

    public function getCategoryById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $id);

        return $this->fetchOne($stmt);
    }

    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        $stmt->bind_param("si", $name, $id);

        return $this->run($stmt);
    }

    public function addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products
            (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("iisdsss", $cat, $brand, $title, $price, $desc, $image_filename, $keywords);

        return $this->run($stmt);
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords) {
        $stmt = $this->conn->prepare(
            "UPDATE products
            SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,
                product_desc = ?, product_image = ?, product_keywords = ?
            WHERE product_id = ?"
        );

        $stmt->bind_param("iisdsssi", $cat, $brand, $title, $price, $desc, $image_filename, $keywords, $id);

        return $this->run($stmt);
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_id = ?"
        );

        $stmt->bind_param("i", $id);

        return $this->fetchOne($stmt);
    }

    public function getAllProducts() {
        $stmt = $this->conn->prepare(
            "SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            ORDER BY p.product_id DESC"
        );

        return $this->fetchAll($stmt);
    }

    public function getFeaturedProducts($limit = 6) {
        $limit = max(1, (int) $limit);

        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY RAND() LIMIT ?");
        $stmt->bind_param("i", $limit);

        return $this->fetchAll($stmt);
    }

    public function getProductsByCategory($cat_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_cat = ? ORDER BY product_id DESC");
        $stmt->bind_param("i", $cat_id);

        return $this->fetchAll($stmt);
    }

    public function getProductsByBrand($brand_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_brand = ? ORDER BY product_id DESC");
        $stmt->bind_param("i", $brand_id);

        return $this->fetchAll($stmt);
    }

    public function searchProducts($query) {
        $like = '%' . addcslashes($query, '%_\\') . '%';

        $stmt = $this->conn->prepare(
            "SELECT * FROM products
            WHERE product_title LIKE ? OR product_keywords LIKE ?
            ORDER BY product_id DESC"
        );

        $stmt->bind_param("ss", $like, $like);

        return $this->fetchAll($stmt);
    }
}
?>
