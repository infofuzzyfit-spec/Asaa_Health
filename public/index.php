<?php
/**
 * Front Controller
 * Main entry point for the application
 */

// Start session
session_start();

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Define constants if not already defined
if (!defined('APP_NAME')) {
    define('APP_NAME', $_ENV['APP_NAME'] ?? 'Asaa Health Care');
    define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
    define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
    define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
    define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
    define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
    define('SMTP_ENCRYPTION', $_ENV['SMTP_ENCRYPTION'] ?? 'tls');
    define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@asaahc.com');
    define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'Asaa Health Care');
}

// Load configuration
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../config/payhere.php';

// Load core classes
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Validator.php';

// Load middleware
require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/middleware/RBACMiddleware.php';
require_once __DIR__ . '/../app/middleware/CSRFMiddleware.php';

// Load Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Create request and response objects
$request = new Request();
$response = new Response();

// Create router instance
$router = new Router();

// Authentication routes
$router->get('/login', 'Auth@showLogin');
$router->post('/login', 'Auth@login');
$router->get('/register', 'Auth@showRegister');
$router->post('/register', 'Auth@register');
$router->get('/logout', 'Auth@logout');
$router->get('/verify-email', 'Auth@verifyEmail');
$router->post('/resend-verification', 'Auth@resendVerification');
$router->get('/forgot-password', 'Auth@showForgotPassword');
$router->post('/forgot-password', 'Auth@forgotPassword');
$router->get('/reset-password', 'Auth@showResetPassword');
$router->post('/reset-password', 'Auth@resetPassword');
$router->get('/change-password', 'Auth@showChangePassword', ['App\\Middleware\\AuthMiddleware']);
$router->post('/change-password', 'Auth@changePassword', ['App\\Middleware\\AuthMiddleware']);
$router->get('/profile', 'Auth@showProfile', ['App\\Middleware\\AuthMiddleware']);
$router->post('/profile', 'Auth@updateProfile', ['App\\Middleware\\AuthMiddleware']);

// Home page
$router->get('/', function($request, $response) {
    return $response->redirect('/login');
});

// Dashboard routes (protected)
$router->get('/dashboard', function($request, $response) {
    return $response->redirect('/login');
}, ['App\\Middleware\\AuthMiddleware']);

// Admin dashboard
$router->get('/admin/dashboard', function($request, $response) {
    return $response->view('admin/dashboard', ['title' => 'Admin Dashboard - ' . APP_NAME]);
}, ['App\\Middleware\\AuthMiddleware@admin']);

// Doctor dashboard
$router->get('/doctor/dashboard', function($request, $response) {
    return $response->view('doctor/dashboard', ['title' => 'Doctor Dashboard - ' . APP_NAME]);
}, ['App\\Middleware\\AuthMiddleware@doctor']);

// Staff dashboard
$router->get('/staff/dashboard', function($request, $response) {
    return $response->view('staff/dashboard', ['title' => 'Staff Dashboard - ' . APP_NAME]);
}, ['App\\Middleware\\AuthMiddleware@staff']);

// Patient dashboard
$router->get('/patient/dashboard', function($request, $response) {
    return $response->view('patient/dashboard', ['title' => 'Patient Dashboard - ' . APP_NAME]);
}, ['App\\Middleware\\AuthMiddleware@patient']);

// Unauthorized page
$router->get('/unauthorized', function($request, $response) {
    return $response->view('errors/unauthorized', ['title' => 'Unauthorized - ' . APP_NAME]);
});

// Dispatch the request
try {
    $router->dispatch($request, $response);
} catch (Exception $e) {
    error_log('Router Error: ' . $e->getMessage());
    $response->json(['error' => 'Internal server error'], 500);
}