<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'CSRF token invalid.';
        header('Location: youth-signup.php');
        exit;
    }
    $data = $_POST;
    $errors = validateYouthSignup($data);
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: youth-signup.php');
        exit;
    }
    $pdo = getDBConnection();
    if (youthExists($pdo, $data['email'])) {
        $_SESSION['error'] = 'Email already exists.';
        header('Location: youth-signup.php');
        exit;
    }
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO youth (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['full_name'], $data['email'], $hashedPassword]);
    sendVerificationEmail($data['email'], $data['full_name'], bin2hex(random_bytes(16)), 'youth');
    $_SESSION['success'] = 'Account created! Check your email.';
    header('Location: youth-login.php');
    exit;
} else {
    header('Location: youth-signup.php');
    exit;
}