<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM organizations");
    $count = $stmt->fetchColumn();
    echo "<br>Organizations count: $count (should be 2 after first run)";

    $stmt = $pdo->query("SELECT org_name FROM organizations LIMIT 1");
    $row = $stmt->fetch();
    echo "<br>First organization: " . ($row ? htmlspecialchars($row['org_name']) : 'None');
} catch (PDOException $e) {
    echo "❌ Test failed: " . htmlspecialchars($e->getMessage());
}
?>
