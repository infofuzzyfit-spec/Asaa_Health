<?php

/**
 * Email Configuration
 * PHPMailer settings
 */

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');

// Email Settings
define('FROM_EMAIL', 'noreply@asaahealthcare.com');
define('FROM_NAME', 'ASAA Healthcare System');
define('REPLY_TO_EMAIL', 'support@asaahealthcare.com');

// Email Templates Path
define('EMAIL_TEMPLATES_PATH', __DIR__ . '/../views/email-templates/');
