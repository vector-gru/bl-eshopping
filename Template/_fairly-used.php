<!-- Fairly Used -->
<?php
    global $product;
    global $Cart;

    // Get only fairly used products
    $product_shuffle = $product->getProductsByCategory('fairly_used');

    // request method post
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['fairly_used_submit'])){
            // call method addToCart
            $Cart->addToCart($_POST['user_id'], $_POST['item_id']);
        }
    }
?>

<section id="fairly-used">
    <div class="container">
        <h4 class="font-rubik font-size-20">Fairly Used</h4>
        <hr>

        <!-- owl carousel -->
        <div class="owl-carousel owl-theme">
            <?php foreach ($product_shuffle as $item) { 
                $primary_image = $item['primary_image'] ?? $item['item_image'] ?? "./assets/products/1.png";
                $savings_amount = $product->getSavingsAmount($item['item_id']);
                $savings_percentage = $product->getSavingsPercentage($item['item_id']);
            ?>
            <div class="item py-2 bg-light">
                <div class="product font-rale">
                    <a href="<?php printf('%s?item_id=%s', 'product.php',  $item['item_id']); ?>">
                        <img src="<?php echo $primary_image; ?>" alt="<?php echo htmlspecialchars($item['item_name'] ?? "product"); ?>" class="img-fluid">
                    </a>
                    <div class="text-center">
                        <h6><?php echo htmlspecialchars($item['item_name'] ?? "Unknown"); ?></h6>
                        <div class="rating text-warning font-size-12">
                            <span><i class="fas fa-star"></i></span>
                            <span><i class="fas fa-star"></i></span>
                            <span><i class="fas fa-star"></i></span>
                            <span><i class="fas fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                        </div>
                        <div class="price py-2">
                            <?php if ($item['old_price'] && $item['old_price'] > $item['item_price']): ?>
                                <span class="text-muted text-decoration-line-through"><?php echo $product->formatPrice($item['old_price'], $item['currency']); ?></span><br>
                            <?php endif; ?>
                            <span class="text-danger fw-bold"><?php echo $product->formatPrice($item['item_price'], $item['currency']); ?></span>
                            <?php if ($savings_amount > 0): ?>
                                <br><small class="text-success">Save <?php echo $product->formatPrice($savings_amount, $item['currency']); ?> (<?php echo $savings_percentage; ?>% off)</small>
                            <?php endif; ?>
                        </div>
                        <?php
                            if (!isset($_SESSION['user_id'])) {
                                echo '<a href="auth/login.php" class="btn btn-warning font-size-12">Add to Cart</a>';
                            } else {
                                echo '<form method="post">
                                        <input type="hidden" name="item_id" value="'.$item['item_id'].'">
                                        <input type="hidden" name="user_id" value="'.$_SESSION['user_id'].'">
                                        <button type="submit" name="fairly_used_submit" class="btn btn-warning font-size-12">Add to Cart</button>
                                    </form>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <!-- !owl carousel -->
    </div>
</section>

<script>
$(document).ready(function() {
    // Initialize Fairly Used carousel
    $("#fairly-used .owl-carousel").owlCarousel({
        loop: true,
        nav: false,
        dots: true,
        responsive : {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000 : {
                items: 5
            }
        }
    });
});
</script>
<!-- !Fairly Used --> 