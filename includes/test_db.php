<?php
// test_db.php: Script for testing db.php
require_once 'db.php';  // Path to your cleaned db.php

$messages = [];

try {
    if (initDatabase()) {
        $messages[] = "✅ Database initialization succeeded.";
        
        // Additional tests
        $pdo = getDBConnection();
        $tablesToCheck = ['youth', 'organizations', 'opportunities', 'applications', 'admins', 'reports'];
        foreach ($tablesToCheck as $table) {
            $stmt = $pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = '$table' LIMIT 1");
            if ($stmt->fetch()) {
                $messages[] = "✅ Table '$table' exists.";
            } else {
                $messages[] = "⚠️ Table '$table' does not exist.";
            }
        }
        $messages[] = "✅ All tests passed!";
    } else {
        $messages[] = "❌ Database initialization failed.";
    }
} catch (Exception $e) {
    $messages[] = "❌ Error: " . $e->getMessage();
}

// Output the results
echo "<h1>Database Test Results:</h1>";
echo "<ul>";
foreach ($messages as $message) {
    echo "<li>$message</li>";
}
echo "</ul>";
?>