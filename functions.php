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

