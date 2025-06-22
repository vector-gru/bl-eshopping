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

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data
$item_id = $_POST['item_id'] ?? null;
$quantity = $_POST['quantity'] ?? null;
$action = $_POST['action'] ?? null; // 'increase' or 'decrease'

if (!$item_id || !$action) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    $conn = getDBConnection();
    
    // Get current quantity
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND item_id = ?");
    $stmt->execute([$_SESSION['user_id'], $item_id]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current) {
        throw new Exception('Item not found in cart');
    }
    
    $current_quantity = (int)$current['quantity'];
    $new_quantity = $current_quantity;
    
    // Calculate new quantity
    if ($action === 'increase') {
        $new_quantity = $current_quantity + 1;
        if ($new_quantity > 10) {
            throw new Exception('Maximum quantity is 10');
        }
    } elseif ($action === 'decrease') {
        $new_quantity = $current_quantity - 1;
        if ($new_quantity < 1) {
            // Remove item from cart if quantity becomes 0
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND item_id = ?");
            $stmt->execute([$_SESSION['user_id'], $item_id]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Item removed from cart',
                'quantity' => 0,
                'removed' => true
            ]);
            exit();
        }
    }
    
    // Update quantity in database
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ?");
    $stmt->execute([$new_quantity, $_SESSION['user_id'], $item_id]);
    
    // Get updated cart data for response
    $stmt = $conn->prepare("
        SELECT c.*, p.item_price, p.item_name, p.currency 
        FROM cart c 
        JOIN product p ON c.item_id = p.item_id 
        WHERE c.user_id = ? AND c.item_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $item_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate new total for this item
    $item_total = $cart_item['item_price'] * $new_quantity;
    
    // Get cart total
    $stmt = $conn->prepare("
        SELECT SUM(c.quantity * p.item_price) as cart_total 
        FROM cart c 
        JOIN product p ON c.item_id = p.item_id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_total = $stmt->fetch(PDO::FETCH_ASSOC)['cart_total'] ?? 0;
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Quantity updated successfully',
        'quantity' => $new_quantity,
        'item_total' => $item_total,
        'cart_total' => $cart_total,
        'currency' => $cart_item['currency'] ?? 'XAF'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 