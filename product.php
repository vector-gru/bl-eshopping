<?php
    ob_start();
    //include header.php file
    include ('header.php');
?>

<?php

    /* include products section */
    include ('Template/_products.php');
    /* !include products section */

    /* include top-sale section */
    include ('Template/_top-sale.php');
    /* !include top-sale section */



?>

<?php
    //include footer.php file
    include ('footer.php');
?>
