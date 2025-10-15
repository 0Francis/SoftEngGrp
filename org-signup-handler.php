<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'CSRF token invalid.';
        header('Location: org-signup.php');
        exit;
    }
    $data = $_POST;
    $pdo = getDBConnection();
    if (userExists($pdo, $data['orgEmail'])) {
        $_SESSION['error'] = 'Email already exists.';
        header('Location: org-signup.php');
        exit;
    }
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO organizations (org_name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['orgName'], $data['orgEmail'], $hashedPassword]);
    $_SESSION['success'] = 'Organization account created!';
    header('Location: org-login.php');
    exit;
} else {
    header('Location: org-signup.php');
    exit;
}
