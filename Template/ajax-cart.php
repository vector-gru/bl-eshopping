<?php
    // require MySQL Connection
    require ('../database/DBController.php');
    require ('../database/Product.php');
    require ('../database/Cart.php');

    use database\DBController;
    use database\Product;
    use database\Cart;

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Debug session
    $session_debug = array(
        'session_id' => session_id(),
        'session_status' => session_status(),
        'session_name' => session_name(),
        'session_save_path' => session_save_path(),
        'session_cookie_params' => session_get_cookie_params(),
        'session_data' => $_SESSION
    );

    // DBController object
    $db = new DBController();

    // Check database connection
    if ($db->con->connect_error) {
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $db->con->connect_error,
            'debug' => $session_debug
        ]));
    }

    // Product object
    $product = new Product($db);

    // Cart object
    $Cart = new Cart($db);

    if(isset($_POST['item_id']) && isset($_POST['user_id'])){
        try {
            // Debug information
            $debug_info = array(
                'session_debug' => $session_debug,
                'post_data' => $_POST,
                'server_data' => array(
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
                    'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? 'not set',
                    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                    'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT']
                )
            );

            // Verify user is logged in
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }

            // Verify user_id matches session
            if ($_POST['user_id'] != $_SESSION['user_id']) {
                throw new Exception('User ID mismatch. Session: ' . $_SESSION['user_id'] . ', Post: ' . $_POST['user_id']);
            }

            // First check if item is already in cart
            $cart = $product->getData('cart');
            $in_cart = false;
            foreach($cart as $item) {
                if($item['item_id'] == $_POST['item_id']) {
                    $in_cart = true;
                    break;
                }
            }
            
            if($in_cart) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Item already in cart',
                    'debug' => $debug_info
                ]);
            } else {
                // Try to add to cart
                $result = $Cart->addToCart($_POST['user_id'], $_POST['item_id']);
                
                if ($result === false) {
                    // Get the last database error
                    $db_error = $db->con->error;
                    throw new Exception("Database error: " . $db_error);
                }
                
                echo json_encode([
                    'success' => $result, 
                    'message' => $result ? 'Added to cart' : 'Failed to add to cart',
                    'debug' => $debug_info
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage(),
                'debug' => $debug_info ?? []
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required parameters',
            'debug' => [
                'post_data' => $_POST,
                'session_data' => $_SESSION,
                'session_debug' => $session_debug
            ]
        ]);
    }
?> 