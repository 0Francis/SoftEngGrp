<?php
// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

// Database (PostgreSQL)
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);
define('DB_PORT', $env['DB_PORT']);

// Email (SMTP config for PHPMailer)
define('SMTP_HOST', $env['SMTP_HOST']);
define('SMTP_PORT', $env['SMTP_PORT']);
define('SMTP_USER', $env['SMTP_USER']);
define('SMTP_PASS', $env['SMTP_PASS']);
define('FROM_EMAIL', $env['FROM_EMAIL']);
define('FROM_NAME', $env['FROM_NAME']);

// Site configuration
define('SITE_URL', $env['SITE_URL']);
define('SESSION_NAME', $env['SESSION_NAME']);

// Security
define('CSRF_SECRET', $env['CSRF_SECRET']);

// Initialize session safely
session_name(SESSION_NAME);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);  // Set to 0 for local testing then 1 in production🫵🏼
ini_set('session.use_strict_mode', 1);
?>
