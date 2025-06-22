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

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$conn = getDBConnection();
$product_id = (int)$_GET['product_id'];

try {
    $stmt = $conn->prepare("SELECT id, image_path, image_name, is_primary, sort_order FROM product_images WHERE item_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$product_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($images);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 