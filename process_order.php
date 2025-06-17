<?php
session_start();
require_once 'database/db_connect.php';
require_once 'database/OrderHelper.php';
require_once 'auth/admin_auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    try {
        $conn->beginTransaction();
        
        // Get cart items
        $stmt = $conn->prepare("
            SELECT c.*, p.item_price, p.currency 
            FROM cart c 
            JOIN product p ON c.item_id = p.item_id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($cart_items)) {
            throw new Exception("Cart is empty");
        }
        
        // Calculate total amount
        $total_amount = 0;
        $currency = $cart_items[0]['currency']; // Use the currency from the first item
        
        foreach ($cart_items as $item) {
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
        
        foreach ($cart_items as $item) {
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
        
        // Get order items for WhatsApp message
        $stmt = $conn->prepare("
            SELECT p.item_name, oi.quantity 
            FROM order_items oi 
            JOIN product p ON oi.item_id = p.item_id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format items for WhatsApp message
        $items_text = "";
        foreach ($order_items as $item) {
            $items_text .= "- {$item['item_name']} (Qty: {$item['quantity']})\n";
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
        
        $conn->commit();
        
        // Redirect to WhatsApp with the order details
        $whatsapp_url = "https://wa.me/{$whatsapp_number}?text=" . urlencode($message);
        header("Location: {$whatsapp_url}");
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error processing order: " . $e->getMessage();
        header('Location: cart.php');
        exit();
    }
} else {
    header('Location: cart.php');
    exit();
}
?> 