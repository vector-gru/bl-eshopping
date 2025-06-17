<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    return; // Don't show orders section if not logged in
}

require_once 'database/db_connect.php';
$conn = getDBConnection();

// Get user's orders
$stmt = $conn->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(p.item_name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN product p ON oi.item_id = p.item_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$user_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="my-orders" class="py-3 mb-5">
    <div class="container-fluid w-75">
        <h5 class="font-baloo font-size-20">My Orders</h5>
        
        <?php if (empty($user_orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No orders yet</h6>
                <p class="text-muted">Start shopping to see your orders here!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($user_orders as $order): ?>
                    <div class="col-12 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <h6 class="font-baloo mb-1">Order #<?php echo $order['order_number']; ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1 font-size-14"><?php echo htmlspecialchars($order['items']); ?></p>
                                        <small class="text-muted"><?php echo count(explode(',', $order['items'])); ?> item(s)</small>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="badge bg-<?php 
                                            echo $order['status'] === 'completed' ? 'success' : 
                                                ($order['status'] === 'processing' ? 'info' : 
                                                ($order['status'] === 'cancelled' ? 'danger' : 'warning')); 
                                        ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h6 class="font-baloo text-danger mb-0">
                                            <?php echo $order['currency'] . ' ' . number_format($order['total_amount'], 2); ?>
                                        </h6>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#orderDetailsModal"
                                                data-order='<?php echo htmlspecialchars(json_encode($order)); ?>'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p id="modal-order-info"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Items</h6>
                        <div id="modal-order-items"></div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Contact Admin</h6>
                                <p class="mb-2">
                                    <strong>WhatsApp:</strong> +237678509520<br>
                                    <small class="text-muted">You can copy this order and forward it to admin over WhatsApp</small>
                                </p>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" id="copy-order-btn">
                                        <i class="fas fa-copy me-2"></i>Copy Order
                                    </button>
                                    <a href="https://wa.me/237678509520" class="btn btn-outline-success" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>Open WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle order details modal
document.querySelectorAll('[data-bs-target="#orderDetailsModal"]').forEach(button => {
    button.addEventListener('click', function() {
        const order = JSON.parse(this.dataset.order);
        
        // Update order information
        document.getElementById('modal-order-info').innerHTML = `
            <strong>Order Number:</strong> #${order.order_number}<br>
            <strong>Date:</strong> ${new Date(order.created_at).toLocaleString()}<br>
            <strong>Status:</strong> <span class="badge bg-${order.status === 'completed' ? 'success' : 
                (order.status === 'processing' ? 'info' : 
                (order.status === 'cancelled' ? 'danger' : 'warning'))}">${order.status}</span><br>
            <strong>Total:</strong> ${order.currency} ${parseFloat(order.total_amount).toFixed(2)}
        `;
        
        // Update order items
        document.getElementById('modal-order-items').innerHTML = order.items;
        
        // Store order data for copy functionality
        window.currentOrderData = order;
    });
});

// Handle copy order button
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'copy-order-btn') {
        const order = window.currentOrderData;
        if (order) {
            // Create WhatsApp message
            const message = `Order #${order.order_number}\n\nItems: ${order.items}\nTotal: ${order.currency} ${parseFloat(order.total_amount).toFixed(2)}\nStatus: ${order.status}`;
            
            // Copy to clipboard
            copyToClipboard(message);
            
            // Show notification
            showNotification('Order details copied to clipboard!', 'success');
        }
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