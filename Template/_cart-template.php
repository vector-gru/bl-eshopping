  <!-- Shopping cart section  -->
 <?php
     global $product;
     global $Cart;

 if ($_SERVER['REQUEST_METHOD'] == 'POST'){
     if (isset($_POST['delete-cart-submit'])){
         $deletedrecord = $Cart->deleteCart($_POST['item_id']);
     }

    // save for later
     if (isset($_POST['wishlist-submit'])){
         $Cart->saveForLater($_POST['item_id']);
     }
 }

 ?>

<section id="cart" class="py-3 mb-5">
    <div class="container-fluid w-75">
        <h5 class="font-baloo font-size-20">Shopping Cart</h5>

        <!--  shopping cart items   -->
        <div class="row">
            <div class="col-sm-9">
                <?php
                    foreach ($product->getData('cart') as $item) :
                        $cart = $product->getProduct($item['item_id']);
                        $subTotal[] = array_map(function ($item) use ($product) {
                            $primary_image = $item['primary_image'] ?? $item['item_image'] ?? "./assets/products/1.png";
                ?>
                    <!-- cart item -->
                    <div class="row border-top py-3 mt-3">
                        <div class="col-sm-2">
                            <img src="<?php echo $primary_image; ?>" style="height: 120px;" alt="<?php echo htmlspecialchars($item['item_name'] ?? "cart item"); ?>" class="img-fluid">
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
                                <div class="d-flex font-rale w-25">
                                    <button class="qty-up border bg-light" data-id="<?php echo $item['item_id'] ?? '0'; ?>"><i class="fas fa-angle-up"></i></button>
                                    <input type="text" data-id="<?php echo $item['item_id'] ?? '0'; ?>" class="qty_input border px-2 w-100 bg-light" disabled value="1" placeholder="1">
                                    <button data-id="<?php echo $item['item_id'] ?? '0'; ?>" class="qty-down border bg-light"><i class="fas fa-angle-down"></i></button>
                                </div>
                                <form method="post">
                                    <input type="hidden" value="<?php echo $item['item_id'] ?? 0; ?>" name="item_id">
                                    <button type="submit" name="delete-cart-submit" class="btn font-baloo text-danger px-3 border-right">Delete</button>
                                </form>

                                <form method="post">
                                    <input type="hidden" value="<?php echo $item['item_id'] ?? 0; ?>" name="item_id">
                                    <button type="submit" name="wishlist-submit" class="btn font-baloo text-danger">Save for Later</button>
                                </form>

                            </div>
                            <!-- !product qty -->

                        </div>

                        <div class="col-sm-2 text-right">
                            <div class="font-size-20 text-danger font-baloo">
                                <?php echo $product->formatPrice($item['item_price'] ?? 0, $item['currency'] ?? 'XAF'); ?>
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

            <!-- subtotal section-->
            <div class="col-sm-3">
                <div class="sub-total border text-center mt-2">
                    <h6 class="font-size-12 font-rale text-success py-3"><i class="fas fa-check"></i> Your order is eligible for FREE Delivery.</h6>
                    <div class="border-top py-4">
                        <h5 class="font-baloo font-size-20">Subtotal (<?php echo isset($subTotal) ? count($subTotal) : 0 ; ?> item(s)):&nbsp; <span class="text-danger"><?php echo $product->formatPrice(isset($subTotal)? $Cart->getSum($subTotal) : 0, 'XAF'); ?></span> </h5>
                        <button type="button" class="btn btn-warning mt-3" data-bs-toggle="modal" data-bs-target="#orderConfirmationModal" onclick="console.log('Button clicked');">
                            Proceed to Buy
                        </button>
                    </div>
                </div>
            </div>
            <!-- !subtotal section-->
        </div>
        <!--  !shopping cart items   -->
    </div>
</section>
<!-- !Shopping cart section  -->

<!-- Order Confirmation Modal -->
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
// Handle order confirmation modal
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const modal = document.getElementById('orderConfirmationModal');
    console.log('Modal element:', modal);
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function () {
            console.log('Modal show event triggered');
            // Get cart data from PHP
            const cartItems = <?php 
                $cartData = $product->getData('cart');
                echo json_encode($cartData ?: []); 
            ?>;
            const products = <?php 
                $productData = $product->getData('product');
                echo json_encode($productData ?: []); 
            ?>;
            
            console.log('Cart items:', cartItems);
            console.log('Products:', products);
            
            let itemsHtml = '';
            let totalAmount = 0;
            let itemCount = 0;
            let currency = 'XAF';
            
            if (cartItems && cartItems.length > 0) {
                cartItems.forEach(cartItem => {
                    const product = products.find(p => p.item_id == cartItem.item_id);
                    if (product) {
                        const itemTotal = parseFloat(product.item_price) * parseInt(cartItem.quantity);
                        totalAmount += itemTotal;
                        itemCount += parseInt(cartItem.quantity);
                        currency = product.currency || 'XAF';
                        
                        itemsHtml += `
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                                <div>
                                    <strong>${product.item_name || 'Unknown Product'}</strong><br>
                                    <small class="text-muted">Qty: ${cartItem.quantity}</small>
                                </div>
                                <div class="text-end">
                                    <strong>${currency} ${itemTotal.toFixed(2)}</strong><br>
                                    <small class="text-muted">${currency} ${parseFloat(product.item_price).toFixed(2)} each</small>
                                </div>
                            </div>
                        `;
                    }
                });
            }
            
            // Update modal content
            document.getElementById('order-items-summary').innerHTML = itemsHtml;
            document.getElementById('order-details-summary').innerHTML = `
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
        });
    }
});
</script>