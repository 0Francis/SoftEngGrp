<?php


// Database (PostgreSQL)
define('DB_HOST', 'localhost');
define('DB_NAME', 'edubridge_db');
define('DB_USER', 'postgres');
define('DB_PASS', 'psqlrafamu05//');
define('DB_PORT', '5432');  

// Email (SMTP config for PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'francis.wainaina@strathmore.edu');
define('SMTP_PASS', 'ucvzcdrjulmxtnbx');
define('FROM_EMAIL', 'francis.wainaina@strathmore.edu');
define('FROM_NAME', 'EduBridge');
define('SITE_URL', 'http://localhost/EduBridge/SoftEngGrp/includes/'); 
define('SESSION_NAME', 'edubridge_session');
session_name(SESSION_NAME);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);  // 0 for local testing
ini_set('session.use_strict_mode', 1); 

// CSRF secret (generate a strong one; change per env)
define('CSRF_SECRET', 'your-random-csrf-secret-key-here-32-chars-min');
?>
