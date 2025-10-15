<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'CSRF token invalid.';
        header('Location: youth-login.php');
        exit;
    }
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT youth_id, password FROM youth WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['youth_id'];
        $_SESSION['role'] = 'youth';
        header('Location: youth-homepage.php');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: youth-login.php');
        exit;
    }
} else {
    header('Location: youth-login.php');
    exit;
}