<?php
// test_functions.php - Test with functions.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'functions.php';  // Loads everything (including autoload)

// Run tests
echo "<h5>Functions Test</h5>";
startSecureSession();
echo "• Session started. ID: " . session_id() . "<br>";
echo "• Logged in? " . (isLoggedIn() ? 'Yes' : 'No') . "<br>";

echo "<h5>CSRF Token Test</h5>";
$csrf = generateCSRFToken();
echo "• CSRF Token: $csrf<br>";

echo "<h5>DB User Check Test</h5>";
$pdo = getDBConnection();
echo "• Youth exists (non-existent): " . (youthExists($pdo, 'test@youth.com') ? 'Yes' : 'No') . "<br>";
$table = userExists($pdo, 'hr@techinnovators.com');  // Sample org
echo "• User exists (sample org): " . ($table ? $table : 'No') . "<br>";

echo "<h5>Validation Test</h5>";
$testData = [
    'full_name' => 'John Doe',
    'email' => 'valid@test.com',
    'password' => 'password123',
    'confirm_password' => 'password123',
    'phone' => '+1234567890'
];
$errors = validateYouthSignup($testData);
echo "• Signup validation errors: " . (empty($errors) ? 'None (valid)' : implode(', ', $errors)) . "<br>";

echo "<h5>Email Test (Optional)</h5>";
$emailSent = sendVerificationEmail('test@example.com', 'Test User', 'fake-token', 'youth');
echo "• Email sent? " . ($emailSent ? 'Yes' : 'No (check config/logs if No)') . "<br>";

echo "<h5>Test Complete!</h5>";
?>
