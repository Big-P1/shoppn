<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Accra');

ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error/error.log');

require_once __DIR__ . '/db_cred.php';
require_once __DIR__ . '/db_class.php';

$paystackCred = __DIR__ . '/paystack_cred.php';

if (is_file($paystackCred)) {
    require_once $paystackCred;
}

unset($paystackCred);

if (!defined('PAYSTACK_PUBLIC_KEY')) {
    define('PAYSTACK_PUBLIC_KEY', '');
}

if (!defined('PAYSTACK_SECRET_KEY')) {
    define('PAYSTACK_SECRET_KEY', '');
}

if (!defined('PAYSTACK_API_BASE')) {
    define('PAYSTACK_API_BASE', 'https://api.paystack.co');
}

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

function redirect_back($fallback) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $parts = $referer !== '' ? parse_url($referer) : false;

    if ($parts && isset($parts['scheme'], $parts['host']) && in_array($parts['scheme'], ['http', 'https'], true)) {
        $refererHost = strtolower($parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : ''));
        $currentHost = strtolower($_SERVER['HTTP_HOST'] ?? '');

        if ($refererHost === $currentHost) {
            redirect($referer);
        }
    }

    redirect($fallback);
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

function clean_input($value) {
    return trim(strip_tags((string) ($value ?? '')));
}

function password_is_strong($password) {
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password)
        && preg_match('/[^A-Za-z0-9]/', $password);
}

function paystack_configured() {
    return strpos(PAYSTACK_PUBLIC_KEY, 'pk_') === 0
        && strpos(PAYSTACK_SECRET_KEY, 'sk_') === 0
        && stripos(PAYSTACK_PUBLIC_KEY . PAYSTACK_SECRET_KEY, 'YOUR') === false;
}

function create_payment_reference() {
    $reference = 'SHP' . (int) ($_SESSION['customer_id'] ?? 0) . '-' . time() . '-' . bin2hex(random_bytes(4));
    $_SESSION['pay_ref'] = $reference;

    return $reference;
}

function render_to_string($file, array $vars = []) {
    extract($vars, EXTR_SKIP);
    ob_start();
    include $file;

    return ob_get_clean();
}

function send_html_mail($to, $subject, $html) {
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $domain = preg_replace('/[^A-Za-z0-9.\-]/', '', $_SERVER['SERVER_NAME'] ?? 'localhost');
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Shoppn <no-reply@' . ($domain !== '' ? $domain : 'localhost') . '>'
    ];

    $sent = @mail($to, $subject, $html, implode("\r\n", $headers));

    if (!$sent) {
        error_log('Receipt email could not be sent to ' . $to);
    }

    return $sent;
}
?>
