<?php
// Debug order processing
ob_start();
session_start();

echo "Debug: Starting order processing\n";
echo "Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set') . "\n";

require_once 'database/db_connect.php';
echo "Debug: Database connection loaded\n";

try {
    $conn = getDBConnection();
    echo "Debug: Database connection successful\n";
    
    // Test cart query
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id'] ?? 0]);
    $cartCount = $stmt->fetchColumn();
    echo "Debug: Cart items count: " . $cartCount . "\n";
    
    // Test user query
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id'] ?? 0]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Debug: User found: " . ($user ? $user['username'] : 'not found') . "\n";
    
} catch (Exception $e) {
    echo "Debug: Error: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();
echo "Debug output:\n" . $output;
?> 