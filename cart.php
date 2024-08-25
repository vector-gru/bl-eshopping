<?php
global $product;
ob_start();
    //include header.php file
    include ('header.php');
?>

<?php

    /* include cart items if not empty */
    count($product->getData('cart')) ? include ('Template/_cart-template.php') :  include ('Template/notFound/_cart_notFound.php');
    /* !include cart items if not empty */

    /* include wishlist-template section */
//    include ('Template/_wishlist_template.php');
     count($product->getData('wishlist')) ? include ('Template/_wishlist_template.php') :  include ('Template/notFound/_wishlist_notFound.php');

    /* !include wishlist-template section */


    /* include new-arrivals section */
    include ('Template/_new-arrivals.php');
    /* !include new-arrivals section */



?>

<?php
    //include footer.php file
    include ('footer.php');
?>
