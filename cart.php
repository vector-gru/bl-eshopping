<?php
    ob_start();
    //include header.php file
    include ('header.php');
?>

<?php

    /* include cart-template section */
    include ('Template/_cart-template.php');
    /* !include cart-template section */


    /* include new-arrivals section */
    include ('Template/_new-arrivals.php');
    /* !include new-arrivals section */



?>

<?php
    //include footer.php file
    include ('footer.php');
?>
