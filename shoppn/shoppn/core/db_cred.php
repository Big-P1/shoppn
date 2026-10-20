<?php
$isLocal = in_array(
    $_SERVER['SERVER_NAME'] ?? '',
    ['localhost', '127.0.0.1'],
    true
);

if ($isLocal) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'shoppn');
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'pierrette.oduro');
    define('DB_PASS', 'iCloud@123');
    define('DB_NAME', 'ecommerce_2026A_pierrette_oduro');
}