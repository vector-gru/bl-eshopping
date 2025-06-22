<?php


    use database\DBController;
    use database\Product;
    use database\Cart;
    use database\Wishlist;


    // require MySQL Connection
    require ('database/DBController.php');

    //require Product Class
    require ('database/Product.php');

    //require Cart Class
    require ('database/Cart.php');

    //require Wishlist Class
    require ('database/Wishlist.php');

    // DBController object
    $db = new DBController();

    // Product object
    $product = new Product($db);
    $product_shuffle = $product->getData();


    //Cart object
    $Cart = new Cart($db);

    //Wishlist object
    $Wishlist = new Wishlist($db);

    // Make objects globally available
    global $product, $Cart, $Wishlist, $product_shuffle;

function isAdmin() {
    try {
        if (!isset($_SESSION['user_id'])) {
            error_log("isAdmin: No user session");
            return false;
        }

        require_once __DIR__ . '/database/db_connect.php';
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        if (!$stmt) {
            error_log("isAdmin: Failed to prepare statement");
            return false;
        }
        
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("isAdmin check for user {$_SESSION['user_id']}: " . ($result ? json_encode($result) : 'no result'));
        
        return $result && $result['is_admin'] == 1;
    } catch (Exception $e) {
        error_log("isAdmin error: " . $e->getMessage());
        return false;
    }
}

