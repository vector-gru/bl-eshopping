<?php
// Prevent any output before JSON response
ob_start();

session_start();
require_once 'database/db_connect.php';
require_once 'database/OrderHelper.php';

// Clear any output buffer
ob_clean();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'User not logged in']);
        exit();
    } else {
        header('Location: login.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = getDBConnection();
        
        // Get cart items directly from database
        $stmt = $conn->prepare("
            SELECT c.*, p.item_price, p.item_name, p.currency 
            FROM cart c 
            JOIN product p ON c.item_id = p.item_id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($cartItems)) {
            throw new Exception("Cart is empty");
        }
        
        // Calculate total amount
        $total_amount = 0;
        $currency = $cartItems[0]['currency'] ?? 'XAF';
        
        foreach ($cartItems as $item) {
            $total_amount += $item['item_price'] * $item['quantity'];
        }
        
        // Generate unique order number
        $orderHelper = new OrderHelper($conn);
        $orderNumber = $orderHelper->generateOrderNumber();
        
        // Create order
        $stmt = $conn->prepare("
            INSERT INTO orders (order_number, user_id, total_amount, currency, status) 
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$orderNumber, $_SESSION['user_id'], $total_amount, $currency]);
        $order_id = $conn->lastInsertId();
        
        // Create order items
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, item_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($cartItems as $item) {
            $stmt->execute([
                $order_id,
                $item['item_id'],
                $item['quantity'],
                $item['item_price']
            ]);
        }
        
        // Get user details for WhatsApp message
        $stmt = $conn->prepare("SELECT username, phone_number FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format items for WhatsApp message
        $items_text = "";
        foreach ($cartItems as $item) {
            $item_total = $item['item_price'] * $item['quantity'];
            $items_text .= "- {$item['item_name']} (Qty: {$item['quantity']}) - {$currency} " . number_format($item_total, 2) . "\n";
        }
        
        // WhatsApp configuration
        $whatsapp_number = "+237678509520"; // Admin's WhatsApp number
        
        // Create WhatsApp message
        $message = "New Order #{$orderNumber}\n\n";
        $message .= "Customer: {$user['username']}\n";
        $message .= "Phone: {$user['phone_number']}\n\n";
        $message .= "Items:\n{$items_text}\n";
        $message .= "Total: {$currency} " . number_format($total_amount, 2);
        
        // Clear the cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Clear any output buffer before JSON response
        ob_clean();
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'order_number' => $orderNumber,
            'message' => $message,
            'whatsapp_number' => $whatsapp_number,
            'whatsapp_url' => "https://wa.me/{$whatsapp_number}?text=" . urlencode($message)
        ]);
        exit();
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        
        // Clear any output buffer before JSON response
        ob_clean();
        
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => "Error processing order: " . $e->getMessage()]);
        exit();
    }
} else {
    header('Location: cart.php');
    exit();
}
?> 