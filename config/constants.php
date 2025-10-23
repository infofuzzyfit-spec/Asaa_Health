<?php

/**
 * System Constants
 * Application-wide constants
 */

// Application Settings
define('APP_NAME', 'ASAA Healthcare Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Asaa_Health_Care');

// File Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Pagination
define('RECORDS_PER_PAGE', 10);

// Time Settings
define('TIMEZONE', 'Asia/Colombo');
date_default_timezone_set(TIMEZONE);

// Security
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
define('SESSION_LIFETIME', 7200); // 2 hours
define('PASSWORD_MIN_LENGTH', 8);

// Appointment Settings
define('APPOINTMENT_CANCELLATION_HOURS', 3);
define('WORKING_HOURS_START', '08:00');
define('WORKING_HOURS_END', '22:00');
define('LUNCH_BREAK_START', '12:00');
define('LUNCH_BREAK_END', '13:00');

// User Roles
define('ROLE_ADMIN', 'Admin');
define('ROLE_STAFF', 'Staff');
define('ROLE_DOCTOR', 'Doctor');
define('ROLE_PATIENT', 'Patient');

// Appointment Status
define('STATUS_REVIEW', 'REVIEW');
define('STATUS_ACCEPTED', 'ACCEPTED');
define('STATUS_CONSULTING', 'CONSULTING');
define('STATUS_COMPLETED', 'COMPLETED');
define('STATUS_CANCELLED', 'CANCELLED');

// Payment Status
define('PAYMENT_PENDING', 'PENDING');
define('PAYMENT_COMPLETED', 'COMPLETED');

// Payment Methods
define('PAYMENT_CARD', 'CARD');
define('PAYMENT_CASH', 'CASH');
