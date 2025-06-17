  <!-- Shopping cart section  -->
 <?php
     global$product;
     global$Cart;

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
                        $subTotal[] = array_map(function ($item){
                ?>
                    <!-- cart item -->
                    <div class="row border-top py-3 mt-3">
                        <div class="col-sm-2">
                            <img src="<?php echo $item['item_image'] ?? "./assets/products/1.png" ?>" style="height: 120px;" alt="cart" class="img-fluid">
                        </div>
                        <div class="col-sm-8">
                            <h5 class="font-baloo font-size-20"><?php echo $item['item_name'] ?? "Unknown"; ?></h5>
                            <small>by <?php echo $item['item_brand'] ?? "Brand"; ?></small>
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
                                $<span class="product_price" data-id="<?php echo $item['item_id'] ?? '0'; ?>"><?php echo $item['item_price'] ?? 0 ; ?></span>
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
                        <h5 class="font-baloo font-size-20">Subtotal (<?php echo isset($subTotal) ? count($subTotal) : 0 ; ?> item(s)):&nbsp; <span class="text-danger">$<span class="text-danger" id="deal-price"><?php echo  isset($subTotal)? $Cart->getSum($subTotal) : 0 ?></span> </span> </h5>
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
            const itemsSummary = document.getElementById('order-items-summary');
            const detailsSummary = document.getElementById('order-details-summary');
            
            console.log('Items summary element:', itemsSummary);
            console.log('Details summary element:', detailsSummary);
            
            if (itemsSummary) {
                if (itemsHtml) {
                    itemsSummary.innerHTML = itemsHtml;
                } else {
                    itemsSummary.innerHTML = '<p class="text-muted">No items in cart</p>';
                }
            }
            
            if (detailsSummary) {
                detailsSummary.innerHTML = `
                    <div class="d-flex justify-content-between mb-2">
                        <span>Items:</span>
                        <span>${itemCount}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total:</span>
                        <strong class="text-danger">${currency} ${totalAmount.toFixed(2)}</strong>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        After confirming, your order will be copied to clipboard for WhatsApp.
                    </small>
                `;
            }
        });
    }
    
    // Handle order form submission
    const confirmForm = document.getElementById('confirm-order-form');
    if (confirmForm) {
        confirmForm.addEventListener('submit', function(e) {
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
                body: new URLSearchParams(new FormData(this))
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get response as text first
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    
                    if (data.success) {
                        // Copy message to clipboard
                        copyToClipboard(data.message);
                        
                        // Show success message
                        showNotification('Order placed successfully! Order details copied to clipboard.', 'success');
                        
                        // Close modal - works with Bootstrap 4 and 5
                        const modalElement = document.getElementById('orderConfirmationModal');
                        if (modalElement) {
                            // Try Bootstrap 5 method first
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                try {
                                    const modal = bootstrap.Modal.getInstance(modalElement);
                                    if (modal) {
                                        modal.hide();
                                    } else {
                                        // If no instance exists, create one and hide it
                                        const newModal = new bootstrap.Modal(modalElement);
                                        newModal.hide();
                                    }
                                } catch (e) {
                                    console.log('Bootstrap 5 modal close failed, trying alternative method');
                                    // Fallback: hide modal manually
                                    modalElement.classList.remove('show');
                                    modalElement.style.display = 'none';
                                    document.body.classList.remove('modal-open');
                                    const backdrop = document.querySelector('.modal-backdrop');
                                    if (backdrop) {
                                        backdrop.remove();
                                    }
                                }
                            } else {
                                // Fallback for Bootstrap 4 or other versions
                                modalElement.classList.remove('show');
                                modalElement.style.display = 'none';
                                document.body.classList.remove('modal-open');
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) {
                                    backdrop.remove();
                                }
                            }
                        }
                        
                        // Launch WhatsApp with order details
                        setTimeout(() => {
                            window.open(data.whatsapp_url, '_blank');
                        }, 500);
                        
                        // Refresh page after a longer delay to allow WhatsApp to open
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        
                    } else {
                        throw new Error(data.error || 'Unknown error occurred');
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', text);
                    throw new Error('Invalid server response: ' + text.substring(0, 100));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error placing order: ' + error.message, 'error');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Function to copy text to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        // Use modern clipboard API
        navigator.clipboard.writeText(text).then(() => {
            console.log('Text copied to clipboard');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            fallbackCopyToClipboard(text);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyToClipboard(text);
    }
}

// Fallback clipboard function
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        console.log('Text copied to clipboard (fallback)');
    } catch (err) {
        console.error('Fallback copy failed: ', err);
    }
    
    document.body.removeChild(textArea);
}

// Function to show notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>