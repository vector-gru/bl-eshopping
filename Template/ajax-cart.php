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
        $result = $Cart->addToCart($_POST['user_id'], $_POST['item_id']);
        echo json_encode(['success' => $result]);
    }
?> 