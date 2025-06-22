<!-- New Arrivals -->

<?php
    global $product;
    global $Cart;

    // Get only new arrival products
    $product_shuffle = $product->getProductsByCategory('new_arrival');

    // request method post
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['new_arrivals_submit'])){
            // call method addToCart
            $Cart->addToCart($_POST['user_id'], $_POST['item_id']);
        }
    }
?>

<section id="new-phones">
    <div class="container">
        <h4 class="font-rubik font-size-20">New Arrivals</h4>
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
                                } else if (in_array($item['item_id'], $Cart->getCartId($product->getData('cart')) ?? [])){
                                echo '<button type="button" disabled class="btn btn-success font-size-12">In the Cart</button>';
                                } else {
                                    echo '<button type="button" onclick="addToCart(this, '.$item['item_id'].', '.$_SESSION['user_id'].')" class="btn btn-warning font-size-12">Add to Cart</button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } // closing for each function ?>
        </div>
        <!-- !owl carousel -->

    </div>
</section>
<!-- !New Arrivals -->