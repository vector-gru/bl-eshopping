<?php


    use database\DBController;
    use database\Product;

    // require MySQL Connection
    require ('database/DBController.php');

    //require Product Class
    require ('database/Product.php');

    // DBController object
    $db = new DBController();

    // Product object
    $product = new Product($db);

//    print_r($product->getData());