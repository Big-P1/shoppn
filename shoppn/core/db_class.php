<?php
class Database {
    protected $conn;

    public function __construct() {
        require_once __DIR__ . '/db_cred.php';

        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        } catch (mysqli_sql_exception $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            die('Connection failed.');
        }

        if ($this->conn->connect_error) {
            error_log('DB connection failed: ' . $this->conn->connect_error);
            die('Connection failed.');
        }
    }
}
?>
