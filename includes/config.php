<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("❌ Missing .env file at $envPath");
}

$env = parse_ini_file($envPath);
if ($env === false) {
    die("❌ Failed to parse .env file. Please check its syntax.");
}

function env($key, $default = null) {
    global $env;
    return isset($env[$key]) ? trim($env[$key]) : $default;
}

// === Database (PostgreSQL) ===
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', ''));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));
define('DB_PORT', env('DB_PORT', '5432'));

// === Email (SMTP Config) ===
define('SMTP_HOST', env('SMTP_HOST', ''));
define('SMTP_PORT', env('SMTP_PORT', ''));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('FROM_EMAIL', env('FROM_EMAIL', ''));
define('FROM_NAME', env('FROM_NAME', 'EduBridge'));

// Site configuration
define('SITE_URL', $env['SITE_URL']);
define('SESSION_NAME', $env['SESSION_NAME']);

// Security
define('CSRF_SECRET', $env['CSRF_SECRET']);

// === Secure session ===
session_name(SESSION_NAME);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');
ini_set('session.cookie_secure', $isLocal ? 0 : 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
