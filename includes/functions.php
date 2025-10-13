<?php


require_once 'config.php';
require_once 'db.php';  

if (file_exists('../vendor/autoload.php')) { 
    require '../vendor/autoload.php';
} else {
    die("Composer vendor not found. Run 'composer install' in SoftEngGrp folder.");
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();  

function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {  // 30 min inactivity
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.html');
        exit;
    }
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.html');
    exit;
}

// CSRF functions 
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Validation for youth signup (updated for schema)
function validateYouthSignup($data) {
    $errors = [];
    if (empty($data['full_name']) || strlen($data['full_name']) > 150) {
        $errors[] = 'Invalid full name.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 150) {
        $errors[] = 'Invalid email.';
    }
    if (strlen($data['password']) < 6) {
        $errors[] = 'Password too short (min 6 chars).';
    }
    if ($data['password'] !== $data['confirm_password']) {
        $errors[] = 'Passwords don\'t match.';
    }
    // Optional fields
if (!empty($data['phone']) && !preg_match('/^\+?[\d\s\-\$\$]{10,20}$/', $data['phone'])) {        
        $errors[] = 'Invalid phone number.';
    }
    if (!empty($data['education_level']) && strlen($data['education_level']) > 100) {
        $errors[] = 'Education level too long.';
    }
    return $errors;
}

// Email sending (verification for youth)
function sendVerificationEmail($email, $fullName, $token, $role = 'youth') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($email, $fullName);
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your EduBridge Account';
        $verifyLink = SITE_URL . "verify.php?token=$token&role=$role";
        $mail->Body = "
            <h2>Welcome to EduBridge, $fullName!</h2>
            <p>Click <a href='$verifyLink'>here</a> to verify your email and activate your $role account.</p>
            <p>This link expires in 24 hours. If you didn't sign up, ignore this email.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}

// User exists check (checks youth or organizations; returns table name)
function userExists($pdo, $email) {
    $tables = ['youth', 'organizations'];  
    foreach ($tables as $table) {
        $idCol = ($table === 'youth') ? 'youth_id' : 'org_id';
        $stmt = $pdo->prepare("SELECT $idCol FROM $table WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch() !== false) {
            return $table;
        }
    }
    return false;
}

// Specific for youth signup
function youthExists($pdo, $email) {
    $stmt = $pdo->prepare("SELECT youth_id FROM youth WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}
?>
