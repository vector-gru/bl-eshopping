<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../auth/admin_auth.php';
requireAdmin();
require_once '../database/db_connect.php';

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

$conn = getDBConnection();

try {
    // Get user's recent orders
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(p.item_name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN product p ON oi.item_id = p.item_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$_GET['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return orders as JSON
    header('Content-Type: application/json');
    echo json_encode($orders);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 