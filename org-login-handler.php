<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'CSRF token invalid.';
        header('Location: org-login.php');
        exit;
    }
    $email = $_POST['orgEmail'] ?? '';
    $password = $_POST['password'] ?? '';
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT org_id, password FROM organizations WHERE email = ?");
    $stmt->execute([$email]);
    $org = $stmt->fetch();
    if ($org && password_verify($password, $org['password'])) {
        $_SESSION['user_id'] = $org['org_id'];
        $_SESSION['role'] = 'organization';
        header('Location: org-dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: org-login.php');
        exit;
    }
} else {
    header('Location: org-login.php');
    exit;
}