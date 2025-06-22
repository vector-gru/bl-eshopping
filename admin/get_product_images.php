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

if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];
    
    // Get product images
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM product_images WHERE item_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$product_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get product sizes
    $stmt = $conn->prepare("SELECT * FROM product_sizes WHERE item_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$product_id]);
    $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return both images and sizes
    echo json_encode([
        'images' => $images,
        'sizes' => $sizes
    ]);
} else {
    echo json_encode(['error' => 'Product ID not provided']);
}
?> 