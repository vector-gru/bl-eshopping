<!--   product  -->

<?php
    global $product;
    global $Cart;
    global $Wishlist;

    $item_id = $_GET['item_id'] ?? 1;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo '<div class="alert alert-warning" role="alert">
                Please <a href="auth/login.php">login</a> to add items to cart or wishlist.
              </div>';
        $user_id = null;
    } else {
        $user_id = $_SESSION['user_id'];
    }
    
    // request method post
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if (!isset($_SESSION['user_id'])) {
            header("Location: auth/login.php");
            exit();
        }
        
        if (isset($_POST['product_submit'])){
            // call method addToCart
            $Cart->addToCart($_SESSION['user_id'], $_POST['item_id']);
        }
        if (isset($_POST['wishlist_submit'])){
            // call method addToWishlist
            $Wishlist->addToWishlist($_SESSION['user_id'], $_POST['item_id']);
        }
    }

    foreach ($product->getData() as $item) :
        if ($item['item_id'] == $item_id) :
            // Get all product images
            $product_images = $product->getProductImages($item_id);
            $primary_image = $product->getPrimaryImage($item_id) ?: ($product_images[0]['image_path'] ?? "./assets/products/1.png");
            $savings_amount = $product->getSavingsAmount($item_id);
            $savings_percentage = $product->getSavingsPercentage($item_id);
            // Get product sizes
            $product_sizes = $product->getProductSizes($item_id);
?>

<section id="product" class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <!-- Main Product Image -->
                <div class="main-image-container mb-3">
                    <img src="<?php echo $primary_image; ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                         class="img-fluid main-product-image" id="main-product-image">
                </div>
                
                <!-- Product Image Thumbnails -->
                <?php if (count($product_images) > 1): ?>
                <div class="product-thumbnails mb-3">
                    <h6 class="font-baloo mb-2">Product Images</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($product_images as $image): ?>
                        <div class="thumbnail-container">
                            <img src="<?php echo $image['image_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                 class="img-thumbnail product-thumbnail" 
                                 style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                 onclick="changeMainImage('<?php echo $image['image_path']; ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row pt-4 font-size-16 font-baloo">
                    <div class="col-md-4 mb-2">
                        <?php
                        if (!isset($_SESSION['user_id'])) {
                            echo '<a href="auth/login.php" class="btn btn-danger w-100">
                                    <i class="fas fa-bolt"></i> Proceed to Buy
                                  </a>';
                        } else {
                            echo '<a href="cart.php" class="btn btn-danger w-100">
                                    <i class="fas fa-bolt"></i> Proceed to Buy
                                  </a>';
                        }
                        ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <?php
                        if (!isset($_SESSION['user_id'])) {
                            echo '<a href="auth/login.php" class="btn btn-warning w-100">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                  </a>';
                        } else if (in_array($item['item_id'], $Cart->getCartId($product->getData('cart')) ?? [])){
                            echo '<button type="button" disabled class="btn btn-success w-100">
                                    <i class="fas fa-shopping-cart"></i> In Cart
                                  </button>';
                        } else {
                            echo '<button type="button" onclick="addToCart(this, '.$item['item_id'].', '.$_SESSION['user_id'].')" class="btn btn-warning w-100">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                  </button>';
                        }
                        ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <?php
                        if (!isset($_SESSION['user_id'])) {
                            echo '<a href="auth/login.php" class="btn btn-info w-100">
                                    <i class="far fa-heart"></i> Add to Wishlist
                                  </a>';
                        } else if (in_array($item['item_id'], $Wishlist->getWishlistId($product->getData('wishlist')) ?? [])){
                            echo '<button type="button" disabled class="btn btn-success w-100">
                                    <i class="fas fa-heart"></i> In Wishlist
                                  </button>';
                        } else {
                            echo '<button type="button" onclick="addToWishlist(this, '.$item['item_id'].', '.$_SESSION['user_id'].')" class="btn btn-info w-100">
                                        <i class="far fa-heart"></i> Add to Wishlist
                                  </button>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 py-5">
                <h5 class="font-baloo font-size-20"><?php echo htmlspecialchars($item['item_name'] ?? "Unknown"); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($item['item_brand'] ?? "Brand"); ?></small>
                <div class="d-flex">
                    <div class="rating text-warning font-size-12">
                        <span><i class="fas fa-star"></i></span>
                        <span><i class="fas fa-star"></i></span>
                        <span><i class="fas fa-star"></i></span>
                        <span><i class="fas fa-star"></i></span>
                        <span><i class="far fa-star"></i></span>
                    </div>
                    <a href="#" class="px-2 font-rale font-size-14">20,534 ratings | 1000+ answered questions</a>
                </div>
                <hr class="m-0">

                <!---    product price       -->
                <table class="my-3">
                    <?php if ($item['old_price'] && $item['old_price'] > $item['item_price']): ?>
                    <tr class="font-rale font-size-14">
                        <td>M.R.P:</td>
                        <td><strike><?php echo $product->formatPrice($item['old_price'], $item['currency']); ?></strike></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="font-rale font-size-14">
                        <td>Deal Price:</td>
                        <td class="font-size-20 text-danger"><?php echo $product->formatPrice($item['item_price'], $item['currency']); ?></td>
                    </tr>
                    <?php if ($savings_amount > 0): ?>
                    <tr class="font-rale font-size-14">
                        <td>You Save:</td>
                        <td><span class="font-size-16 text-danger"><?php echo $product->formatPrice($savings_amount, $item['currency']); ?> (<?php echo $savings_percentage; ?>% off)</span></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <!---    !product price       -->

                <!-- Stock Status -->
                <div class="mb-3">
                    <?php if ($item['stock_quantity'] > 0): ?>
                        <span class="badge bg-success">In Stock (<?php echo $item['stock_quantity']; ?> available)</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <!--    #policy -->
                <div id="policy">
                    <div class="d-flex">
                        <div class="return text-center mr-5">
                            <div class="font-size-20 my-2 color-second">
                                <span class="fas fa-retweet border p-3 rounded-pill"></span>
                            </div>
                            <a href="#" class="font-rale font-size-12">10 Days <br> Replacement</a>
                        </div>
                        <div class="return text-center mr-5">
                            <div class="font-size-20 my-2 color-second">
                                <span class="fas fa-truck  border p-3 rounded-pill"></span>
                            </div>
                            <a href="#" class="font-rale font-size-12">Nationwide <br>Delivery</a>
                        </div>
                        <div class="return text-center mr-5">
                            <div class="font-size-20 my-2 color-second">
                                <span class="fas fa-check-double border p-3 rounded-pill"></span>
                            </div>
                            <a href="#" class="font-rale font-size-12">Quality<br>Assured</a>
                        </div>
                    </div>
                </div>
                <!--    !policy -->
                <hr>

                <!-- order-details -->
                <div id="order-details" class="font-rale d-flex flex-column text-dark">
                    <small>Delivery within : 2 days from purchase time</small>
                    <small>Sold by <a href="#">B&L Technologies </a>(4.5 out of 5 | 18,198 ratings)</small>
                    <small><i class="fas fa-map-marker-alt color-primary"></i>&nbsp;&nbsp;Deliver to Customer</small>
                </div>
                <!-- !order-details -->

                <div class="row">
                    <div class="col-6">
                        <!-- color -->
                        <div class="color my-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-baloo">Color:</h6>
                                <div class="p-2 color-yellow-bg rounded-circle"><button class="btn font-size-14"></button></div>
                                <div class="p-2 color-primary-bg rounded-circle"><button class="btn font-size-14"></button></div>
                                <div class="p-2 color-second-bg rounded-circle"><button class="btn font-size-14"></button></div>
                            </div>
                        </div>
                        <!-- !color -->
                    </div>
                    <div class="col-6">
                        <!-- product qty section -->
                        <div class="qty d-flex">
                            <h6 class="font-baloo">Qty</h6>
                            <div class="px-4 d-flex font-rale">
                                <button class="qty-up border bg-light" data-id="pro1"><i class="fas fa-angle-up"></i></button>
                                <input type="text" data-id="pro1" class="qty_input border px-2 w-50 bg-light" disabled value="1" placeholder="1">
                                <button data-id="pro1" class="qty-down border bg-light"><i class="fas fa-angle-down"></i></button>
                            </div>
                        </div>
                        <!-- !product qty section -->
                    </div>
                </div>

                <!-- size -->
                <div class="size my-3">
                    <h6 class="font-baloo">Size :</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($product_sizes)): ?>
                            <?php foreach ($product_sizes as $size): ?>
                            <div class="font-rubik border p-2 rounded">
                                <button class="btn p-0 font-size-14"><?php echo htmlspecialchars($size['size_name'] . ': ' . $size['size_value']); ?></button>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="font-rubik border p-2 rounded">
                                <span class="text-muted font-size-14">No sizes available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- !size -->
            </div>

            <div class="col-12">
                <h6 class="font-rubik">Product Description</h6>
                <hr>
                <?php if ($item['item_description']): ?>
                    <p class="font-rale font-size-14"><?php echo nl2br(htmlspecialchars($item['item_description'])); ?></p>
                <?php else: ?>
                    <p class="font-rale font-size-14">No description available for this product.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function changeMainImage(imagePath) {
    document.getElementById('main-product-image').src = imagePath;
    
    // Update active thumbnail
    document.querySelectorAll('.product-thumbnail').forEach(thumb => {
        thumb.classList.remove('border-primary');
        if (thumb.src.includes(imagePath)) {
            thumb.classList.add('border-primary');
        }
    });
}

// Set first thumbnail as active on page load
document.addEventListener('DOMContentLoaded', function() {
    const firstThumbnail = document.querySelector('.product-thumbnail');
    if (firstThumbnail) {
        firstThumbnail.classList.add('border-primary');
    }
});
</script>

<!--   !product  -->

<?php
        endif;
    endforeach;
?>
