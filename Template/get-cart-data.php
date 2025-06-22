<?php
session_start();
require_once '../database/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $conn = getDBConnection();
    
    // Get current cart data with product information
    $stmt = $conn->prepare("
        SELECT c.*, p.item_name, p.item_price, p.old_price, p.item_brand, p.currency,
            (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
        FROM cart c 
        JOIN product p ON c.item_id = p.item_id 
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate cart totals
    $totalAmount = 0;
    $itemCount = 0;
    $currency = 'XAF';
    
    foreach ($cartItems as $item) {
        $totalAmount += $item['item_price'] * $item['quantity'];
        $itemCount += $item['quantity'];
        $currency = $item['currency'] ?? 'XAF';
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'cart_items' => $cartItems,
        'cart_total' => $totalAmount,
        'item_count' => $itemCount,
        'currency' => $currency
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 