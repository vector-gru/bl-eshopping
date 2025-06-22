<!-- Wishlist section  -->
<?php
global $product;
global $Cart;
global $Wishlist;

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset($_POST['delete-cart-submit'])){
            $deletedrecord = $Wishlist->deleteWishlist($_POST['item_id']);
        }

        // get it back to cart
        if (isset($_POST['cart-submit'])){
            $Wishlist->moveToCart($_POST['item_id']);
        }
    }

?>

<section id="cart" class="py-3 mb-5">
    <div class="container-fluid w-75">
        <h5 class="font-baloo font-size-20">Wishlist</h5>

        <!--  shopping cart items   -->
        <div class="row">
            <div class="col-sm-9">
                <?php
                foreach ($product->getData('wishlist') as $item) :
                    $cart = $product->getProduct($item['item_id']);
                    $subTotal[] = array_map(function ($item) use ($product) {
                        $primary_image = $item['primary_image'] ?? $item['item_image'] ?? "./assets/products/1.png";
                        $savings_amount = $product->getSavingsAmount($item['item_id']);
                        $savings_percentage = $product->getSavingsPercentage($item['item_id']);
                        ?>
                        <!-- cart item -->
                        <div class="row border-top py-3 mt-3">
                            <div class="col-sm-2">
                                <img src="<?php echo $primary_image; ?>" style="height: 120px;" alt="<?php echo htmlspecialchars($item['item_name'] ?? "wishlist item"); ?>" class="img-fluid">
                            </div>
                            <div class="col-sm-8">
                                <h5 class="font-baloo font-size-20"><?php echo htmlspecialchars($item['item_name'] ?? "Unknown"); ?></h5>
                                <small>by <?php echo htmlspecialchars($item['item_brand'] ?? "Brand"); ?></small>
                                <!-- product rating -->
                                <div class="d-flex">
                                    <div class="rating text-warning font-size-12">
                                        <span><i class="fas fa-star"></i></span>
                                        <span><i class="fas fa-star"></i></span>
                                        <span><i class="fas fa-star"></i></span>
                                        <span><i class="fas fa-star"></i></span>
                                        <span><i class="far fa-star"></i></span>
                                    </div>
                                    <a href="#" class="px-2 font-rale font-size-14">20,534 ratings</a>
                                </div>
                                <!--  !product rating-->

                                <!-- product qty -->
                                <div class="qty d-flex pt-2">

                                    <form method="post">
                                        <input type="hidden" value="<?php echo $item['item_id'] ?? 0; ?>" name="item_id">
                                        <button type="submit" name="delete-cart-submit" class="btn font-baloo text-danger pl-0 pr-3 border-right">Delete</button>
                                    </form>

                                    <form method="post">
                                        <input type="hidden" value="<?php echo $item['item_id'] ?? 0; ?>" name="item_id">
                                        <button type="submit" name="cart-submit" class="btn font-baloo text-danger">Add to Cart</button>
                                    </form>


                                </div>
                                <!-- !product qty -->

                            </div>

                            <div class="col-sm-2 text-right">
                                <div class="font-size-20 text-danger font-baloo">
                                    <?php if ($item['old_price'] && $item['old_price'] > $item['item_price']): ?>
                                        <div class="text-muted text-decoration-line-through"><?php echo $product->formatPrice($item['old_price'], $item['currency'] ?? 'XAF'); ?></div>
                                    <?php endif; ?>
                                    <div><?php echo $product->formatPrice($item['item_price'] ?? 0, $item['currency'] ?? 'XAF'); ?></div>
                                    <?php if ($savings_amount > 0): ?>
                                        <small class="text-success">Save <?php echo $product->formatPrice($savings_amount, $item['currency'] ?? 'XAF'); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- !cart item -->
                        <?php
                        return $item['item_price'];
                    },$cart); // closing array_map function
                endforeach;

                ?>
            </div>
        </div>
        <!--  !shopping cart items   -->
    </div>
</section>
<!-- !Wishlist section -->