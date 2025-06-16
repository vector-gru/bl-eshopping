<!-- Special Price -->
<?php
    global$product;
    global$Cart;

    $product_shuffle = $product->getData();


    $brand = array_map(function ($pro){ return $pro['item_brand']; }, $product_shuffle);
    $unique = array_unique($brand);
    sort($unique);
    shuffle($product_shuffle);

    // request method post
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['special_price_submit'])){
            // call method addToCart
            $Cart->addToCart($_POST['user_id'], $_POST['item_id']);
        }
    }

    $in_cart = $Cart->getCartId($product->getData('cart'));

?>

<section id="special-price">
    <div class="container">
        <h4 class="font-rubik font-size-20">Special Price</h4>
        <div id="filters" class="button-group text-right font-baloo font-size-16">
            <button class="btn is-checked" data-filter="*">All Brand</button>
            <?php
            array_map(function ($brand){
                printf('<button class="btn" data-filter=".%s">%s</button>', $brand, $brand);
            }, $unique);
            ?>
<!--            <button class="btn" data-filter=".Pixel">Pixel</button>-->
<!--            <button class="btn" data-filter=".Tecno">Tecno</button>-->
<!--            <button class="btn" data-filter=".itel">itel</button>-->
<!--            <button class="btn" data-filter=".Others">Others</button>-->
        </div>

        <div class="grid">
            <?php array_map(function ($item)use($in_cart){?>
                <div class="grid-item border <?php echo $item['item_brand'] ?? "Brand"; ?>">
                <div class="item py-2" style="width: 200px;">
                    <div class="product font-rale">
                        <a href="<?php printf('%s?item_id=%s', 'product.php',  $item['item_id']); ?>"><img src="<?php echo $item['item_image'] ?? "./assets/products/13.png";?>" alt="product1" class="img-fluid"></a>
                        <div class="text-center">
                            <h6><?php echo $item['item_name'] ?? "Unknown"; ?></h6>
                            <div class="rating text-warning font-size-12">
                                <span><i class="fas fa-star"></i></span>
                                <span><i class="fas fa-star"></i></span>
                                <span><i class="fas fa-star"></i></span>
                                <span><i class="fas fa-star"></i></span>
                                <span><i class="far fa-star"></i></span>
                            </div>
                            <div class="price py-2">
                                <span>$<?php echo $item['item_price'] ?? '0';?></span>
                            </div>
                            <?php
                                if (!isset($_SESSION['user_id'])) {
                                    echo '<button type="button" disabled class="btn btn-warning font-size-12">Login to Add to Cart</button>';
                                } else if (in_array($item['item_id'], $in_cart ?? [])){
                                    echo '<button type="button" disabled class="btn btn-success font-size-12">In the Cart</button>';
                                } else {
                                    echo '<button type="button" onclick="addToCart(this, '.$item['item_id'].', '.$_SESSION['user_id'].')" class="btn btn-warning font-size-12">Add to Cart</button>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php }, $product_shuffle) ?>
        </div>
    </div>
</section>
<!-- !Special Price -->