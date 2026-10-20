<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Accra');

ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error/error.log');

require_once __DIR__ . '/db_cred.php';
require_once __DIR__ . '/db_class.php';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (strpos($host, 'localhost') === 0 || strpos($host, '127.0.0.1') === 0) {
    define('BASE_URL', '/shoppn');
} else {
    define('BASE_URL', '/~pierrette.oduro/ecommerce-class/shoppn');
}

unset($host);

function get_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return '0.0.0.0';
    }

    return $ip;
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function upload_image($field, $targetDir, $maxBytes = 2097152) {
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['status' => 'none', 'filename' => null, 'error' => null];
    }

    $file = $_FILES[$field];

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return ['status' => 'error', 'filename' => null, 'error' => 'Image is too large. Maximum size is ' . round($maxBytes / 1048576) . 'MB.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        return ['status' => 'error', 'filename' => null, 'error' => 'Image upload failed. Please try again.'];
    }

    if ($file['size'] > $maxBytes) {
        return ['status' => 'error', 'filename' => null, 'error' => 'Image is too large. Maximum size is ' . round($maxBytes / 1048576) . 'MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return ['status' => 'error', 'filename' => null, 'error' => 'Only JPG, PNG, GIF or WEBP images are allowed.'];
    }

    $base = pathinfo(basename($file['name']), PATHINFO_FILENAME);
    $base = preg_replace('/[^A-Za-z0-9_-]/', '_', $base);
    $base = substr($base === '' ? 'image' : $base, 0, 40);

    $filename = uniqid() . '_' . $base . '.' . $allowed[$mime];
    $destination = rtrim($targetDir, '/\\') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        error_log('Could not move uploaded file to ' . $destination);
        return ['status' => 'error', 'filename' => null, 'error' => 'Could not save the uploaded image.'];
    }

    return ['status' => 'ok', 'filename' => $filename, 'error' => null];
}

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 1;
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect(BASE_URL . '/views/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to access that page.';
        redirect(BASE_URL . '/index.php');
    }
}
?>
