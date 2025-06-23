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
                            echo '<button type="button" class="btn btn-danger w-100" onclick="proceedToBuy('.$item['item_id'].', '.$_SESSION['user_id'].')">
                                    <i class="fas fa-bolt"></i> Proceed to Buy
                                  </button>';
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

// Function to proceed to buy - adds to cart first, then shows modal if cart was empty
function proceedToBuy(itemId, userId) {
    // First check current cart count
    const cartCountElement = document.querySelector('.cart-count');
    const currentCartCount = cartCountElement ? parseInt(cartCountElement.textContent) || 0 : 0;
    
    console.log('Current cart count:', currentCartCount);
    
    // Create form data for adding to cart
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('user_id', userId);

    // Send AJAX request to add to cart
    fetch('Template/ajax-cart.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Cart response:', data);
        
        if (data.success) {
            // Update cart count
            if (cartCountElement) {
                cartCountElement.textContent = currentCartCount + 1;
            }
            
            // If cart was empty before adding this item, show confirmation modal
            if (currentCartCount === 0) {
                console.log('Cart was empty, showing confirmation modal');
                // Wait a moment for the cart to be updated, then show modal
                setTimeout(() => {
                    showCartOrderModal();
                }, 500);
            } else {
                console.log('Cart had items, redirecting to cart page');
                // Redirect to cart page
                window.location.href = 'cart.php';
            }
        } else {
            // If failed, show error message
            let errorMessage = data.message || 'Failed to add item to cart.';
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Cart error:', error);
        alert('An error occurred while adding to cart: ' + error.message);
    });
}

// Function to show cart order modal (reuses existing cart modal functionality)
function showCartOrderModal() {
    console.log('Showing cart order modal...');
    
    // First update the modal data, then show the modal
    updateOrderModal();
    
    // Wait a moment for the data to be fetched, then show modal
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('orderConfirmationModal'));
        modal.show();
    }, 300);
}
</script>

<!-- Order Confirmation Modal (same as cart template) -->
<div class="modal fade" id="orderConfirmationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Your Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6>Order Summary</h6>
                        <div id="order-items-summary"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Order Details</h6>
                                <div id="order-details-summary"></div>
                                <hr>
                                <div class="d-grid gap-2">
                                    <form method="POST" action="process_order.php" id="confirm-order-form">
                                        <button type="submit" class="btn btn-success" id="confirm-order-btn">
                                            <i class="fas fa-check me-2"></i>Confirm Order
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle order form submission for product page (same as cart template)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('confirm-order-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('confirm-order-btn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;
            
            // Submit form via AJAX
            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new FormData(this)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get raw text first
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Raw text that failed to parse:', text);
                    throw new Error('Invalid JSON response from server');
                }
            })
            .then(data => {
                if (data.success) {
                    // Close modal immediately using simple DOM manipulation
                    const modalElement = document.getElementById('orderConfirmationModal');
                    if (modalElement) {
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                    }
                    
                    // Show success message
                    alert('Order confirmed! Redirecting to WhatsApp...');
                    
                    // Open WhatsApp immediately
                    window.open(data.whatsapp_url, '_blank');
                    
                    // Redirect to home page
                    window.location.href = 'index.php';
                } else {
                    throw new Error(data.error || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing order: ' + error.message);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Handle order confirmation modal
    const modal = document.getElementById('orderConfirmationModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function () {
            // Update modal with current cart data
            updateOrderModal();
        });
        
        // Also handle when modal is shown programmatically
        modal.addEventListener('shown.bs.modal', function () {
            // Update modal with current cart data after modal is fully shown
            updateOrderModal();
        });
    }
});

// Global function to update order modal with current cart data (same as cart template)
function updateOrderModal(retryCount = 0) {
    console.log('Updating order modal with cart data... (attempt ' + (retryCount + 1) + ')');
    
    fetch('Template/get-cart-data.php')
        .then(response => response.json())
        .then(data => {
            console.log('Cart data received:', data);
            
            if (data.success) {
                const cartItems = data.cart_items;
                let itemsHtml = '';
                let totalAmount = 0;
                let itemCount = 0;
                let currency = 'XAF';
                
                if (cartItems && cartItems.length > 0) {
                    cartItems.forEach(cartItem => {
                        const itemTotal = parseFloat(cartItem.item_price) * parseInt(cartItem.quantity);
                        totalAmount += itemTotal;
                        itemCount += parseInt(cartItem.quantity);
                        currency = cartItem.currency || 'XAF';
                        
                        itemsHtml += `
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                                <div>
                                    <strong>${cartItem.item_name || 'Unknown Product'}</strong><br>
                                    <small class="text-muted">Qty: ${cartItem.quantity}</small>
                                </div>
                                <div class="text-end">
                                    <strong>${currency} ${itemTotal.toFixed(2)}</strong><br>
                                    <small class="text-muted">${currency} ${parseFloat(cartItem.item_price).toFixed(2)} each</small>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    // If no items and this is the first attempt, retry once
                    if (retryCount === 0) {
                        console.log('No cart items found, retrying in 1 second...');
                        setTimeout(() => {
                            updateOrderModal(1);
                        }, 1000);
                        return;
                    }
                    itemsHtml = '<div class="text-muted">No items in cart</div>';
                }
                
                // Update modal content
                const orderItemsSummary = document.getElementById('order-items-summary');
                const orderDetailsSummary = document.getElementById('order-details-summary');
                
                if (orderItemsSummary) {
                    orderItemsSummary.innerHTML = itemsHtml;
                }
                
                if (orderDetailsSummary) {
                    if (cartItems && cartItems.length > 0) {
                        orderDetailsSummary.innerHTML = `
                            <div class="d-flex justify-content-between mb-2">
                                <span>Items (${itemCount}):</span>
                                <span>${currency} ${totalAmount.toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery:</span>
                                <span class="text-success">FREE</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span>${currency} ${totalAmount.toFixed(2)}</span>
                            </div>
                        `;
                    } else {
                        orderDetailsSummary.innerHTML = `
                            <div class="text-muted">No items to display</div>
                        `;
                    }
                }
            } else {
                console.error('Failed to fetch cart data:', data.message);
                // Show error message in modal
                const orderItemsSummary = document.getElementById('order-items-summary');
                const orderDetailsSummary = document.getElementById('order-details-summary');
                
                if (orderItemsSummary) {
                    orderItemsSummary.innerHTML = '<div class="text-danger">Error loading cart data</div>';
                }
                if (orderDetailsSummary) {
                    orderDetailsSummary.innerHTML = '<div class="text-danger">Please try again</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching cart data:', error);
            // Show error message in modal
            const orderItemsSummary = document.getElementById('order-items-summary');
            const orderDetailsSummary = document.getElementById('order-details-summary');
            
            if (orderItemsSummary) {
                orderItemsSummary.innerHTML = '<div class="text-danger">Error loading cart data</div>';
            }
            if (orderDetailsSummary) {
                orderDetailsSummary.innerHTML = '<div class="text-danger">Please try again</div>';
            }
        });
}
</script>

<!--   !product  -->

<?php
        endif;
    endforeach;
?>
