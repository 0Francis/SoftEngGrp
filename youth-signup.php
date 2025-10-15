<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    startSecureSession();
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'CSRF token invalid.';
        header('Location: youth-signup.html');
        exit;
    }

    $data = $_POST;  // Get all POST data
    $errors = validateYouthSignup($data);
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: youth-signup.html');
        exit;
    }

    $pdo = getDBConnection();
    if (youthExists($pdo, $data['email'])) {
        $_SESSION['error'] = 'Email already exists.';
        header('Location: youth-signup.html');
        exit;
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO youth (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['full_name'], $data['email'], $hashedPassword]);
    sendVerificationEmail($data['email'], $data['full_name'], bin2hex(random_bytes(16)), 'youth');
    $_SESSION['success'] = 'Account created! Check your email to verify.';
    header('Location: youth-login.html');
    exit;
} else {
    header('Location: youth-signup.html');
    exit;
}
?>