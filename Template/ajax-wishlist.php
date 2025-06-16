<?php
    // require MySQL Connection
    require ('../database/DBController.php');
    require ('../database/Product.php');
    require ('../database/Cart.php');

    use database\DBController;
    use database\Product;
    use database\Cart;

    // DBController object
    $db = new DBController();

    // Product object
    $product = new Product($db);

    // Cart object
    $Cart = new Cart($db);

    if(isset($_POST['item_id']) && isset($_POST['user_id'])){
        try {
            // First check if item is already in wishlist
            $wishlist = $product->getData('wishlist');
            $in_wishlist = false;
            foreach($wishlist as $item) {
                if($item['item_id'] == $_POST['item_id']) {
                    $in_wishlist = true;
                    break;
                }
            }
            
            if($in_wishlist) {
                echo json_encode(['success' => true, 'message' => 'Item already in wishlist']);
            } else {
                $result = $Cart->saveForLater($_POST['item_id'], 'wishlist', 'cart');
                echo json_encode(['success' => $result, 'message' => $result ? 'Added to wishlist' : 'Failed to add to wishlist']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    }
?> 