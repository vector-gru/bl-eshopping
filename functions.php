<?php


    use database\DBController;
    use database\Product;
    use database\Cart;


    // require MySQL Connection
    require ('database/DBController.php');

    //require Product Class
    require ('database/Product.php');

    //require Cart Class
    require ('database/Cart.php');

    // DBController object
    $db = new DBController();

    // Product object
    $product = new Product($db);
    $product_shuffle = $product->getData();


    //Cart object
    $Cart = new Cart($db);


    //    print_r($product->getData());