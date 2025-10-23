<?php

/**
 * PayHere Payment Gateway Configuration
 * Sandbox mode settings
 */

// PayHere Sandbox Credentials
define('PAYHERE_MERCHANT_ID', '1221149');
define('PAYHERE_MERCHANT_SECRET', 'YOUR_MERCHANT_SECRET_HERE');
define('PAYHERE_SANDBOX', true);

// PayHere URLs
define('PAYHERE_CHECKOUT_URL', 'https://sandbox.payhere.lk/pay/checkout');
define('PAYHERE_NOTIFY_URL', 'https://yourdomain.com/public/payment/notify.php');
define('PAYHERE_CANCEL_URL', 'https://yourdomain.com/public/payment/cancel.php');
define('PAYHERE_RETURN_URL', 'https://yourdomain.com/public/payment/return.php');

// Currency
define('PAYHERE_CURRENCY', 'LKR');
