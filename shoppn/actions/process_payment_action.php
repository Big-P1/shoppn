<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_login();

$cartPage = BASE_URL . '/views/cart.php';
$failedPage = BASE_URL . '/views/payment_failed.php';

function verify_paystack_transaction($reference) {
    $ch = curl_init(rtrim(PAYSTACK_API_BASE, '/') . '/transaction/verify/' . rawurlencode($reference));

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Cache-Control: no-cache'
        ],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $body = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $httpCode >= 500) {
        error_log('Paystack verify unreachable: ' . $curlError . ' HTTP ' . $httpCode);
        return ['reachable' => false, 'response' => null];
    }

    $decoded = json_decode($body, true);

    return ['reachable' => true, 'response' => is_array($decoded) ? $decoded : null];
}

$reference = trim($_GET['ref'] ?? '');

if ($reference === '' || !preg_match('/^[A-Za-z0-9_.=\-]{6,100}$/', $reference)) {
    $_SESSION['error'] = 'Missing or invalid payment reference.';
    redirect($failedPage);
}

$expectedReference = $_SESSION['pay_ref'] ?? '';

if ($expectedReference === '' || !hash_equals($expectedReference, $reference)) {
    $_SESSION['error'] = 'This payment session is not valid. Please start the payment again from your cart.';
    redirect($failedPage);
}

if (!paystack_configured()) {
    $_SESSION['error'] = 'Payments are not configured yet. Add your Paystack test keys to core/paystack_cred.php.';
    redirect($failedPage);
}

$ip = get_ip();
$customerId = (int) $_SESSION['customer_id'];
$cart = new CartController();

$cartTotal = $cart->getCartTotal($ip);

if ($cartTotal <= 0 || $cart->getCartCount($ip) === 0) {
    unset($_SESSION['pay_ref']);
    $_SESSION['error'] = 'Your cart is empty, so there is nothing to pay for.';
    redirect($cartPage);
}

$verification = verify_paystack_transaction($reference);

if (!$verification['reachable']) {
    $_SESSION['error'] = 'We could not reach Paystack to confirm your payment. If you were charged, please refresh this page in a moment.';
    redirect($failedPage);
}

$response = $verification['response'];
$data = (is_array($response) && isset($response['data']) && is_array($response['data'])) ? $response['data'] : [];

$confirmed = is_array($response)
    && ($response['status'] ?? false) === true
    && ($data['status'] ?? '') === 'success'
    && ($data['reference'] ?? '') === $reference
    && strtoupper($data['currency'] ?? '') === 'GHS'
    && (int) ($data['amount'] ?? 0) === (int) round($cartTotal * 100)
    && strtolower($data['customer']['email'] ?? '') === strtolower($_SESSION['customer_email'] ?? '');

if (!$confirmed) {
    unset($_SESSION['pay_ref']);
    $_SESSION['error'] = 'Paystack did not confirm this payment, so no order was created.';
    redirect($failedPage);
}

$orderId = $cart->processPayment($customerId, $ip, $reference, $cartTotal);

unset($_SESSION['pay_ref']);

if ($orderId === false) {
    $_SESSION['error'] = 'Your payment was confirmed but the order could not be saved. Please contact support with reference ' . $reference . '.';
    redirect($failedPage);
}

$order = $cart->getOrderById($orderId);
$_SESSION['success'] = 'Payment successful. Thank you for shopping with Shoppn!';

if ($order) {
    $body = render_to_string(__DIR__ . '/../views/emails/receipt_email.php', [
        'order' => $order['order'],
        'items' => $order['items']
    ]);

    if (send_html_mail($_SESSION['customer_email'], 'Your Shoppn receipt - INV-' . $order['order']['invoice_no'], $body)) {
        $_SESSION['info'] = 'A receipt has been emailed to ' . $_SESSION['customer_email'] . '.';
    }
}

redirect(BASE_URL . '/views/payment_success.php?order_id=' . $orderId);
?>
