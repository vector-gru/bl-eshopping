<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../auth/admin_auth.php';
requireAdmin();
require_once '../database/db_connect.php';

$conn = getDBConnection();
$message = '';

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        $message = "Order status updated successfully!";
    } catch (PDOException $e) {
        $message = "Error updating order status: " . $e->getMessage();
    }
}

// Get all orders with user and product details
$stmt = $conn->query("
    SELECT o.*, u.username, u.email, u.phone_number,
           GROUP_CONCAT(CONCAT(p.item_name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN product p ON oi.item_id = p.item_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// WhatsApp configuration
$whatsapp_number = "+237678509520"; // Replace with your WhatsApp number
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,.8);
        }
        .admin-sidebar .nav-link:hover {
            color: white;
        }
        .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,.1);
        }
        .order-status {
            min-width: 120px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 admin-sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-box me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i> Users
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="../auth/admin_logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Orders</h2>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Orders Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_number']; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($order['username']); ?></div>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($order['email']); ?><br>
                                                <?php echo htmlspecialchars($order['phone_number']); ?>
                                            </small>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['items']); ?></td>
                                        <td><?php echo $order['currency'] . ' ' . number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <form method="POST" class="order-status">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo urlencode("Order #{$order['order_number']} from {$order['username']}\nItems: {$order['items']}\nTotal: {$order['currency']} {$order['total_amount']}"); ?>" 
                                               class="btn btn-sm btn-success" 
                                               target="_blank">
                                                <i class="fab fa-whatsapp"></i> Contact
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#orderDetailsModal"
                                                    data-order='<?php echo htmlspecialchars(json_encode($order)); ?>'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <h6>Customer Information</h6>
                            <p id="modal-customer-info"></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p id="modal-order-info"></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Order Items</h6>
                        <p id="modal-order-items"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle order details modal
        document.querySelectorAll('[data-bs-target="#orderDetailsModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const order = JSON.parse(this.dataset.order);
                
                // Update customer information
                document.getElementById('modal-customer-info').innerHTML = `
                    <strong>Name:</strong> ${order.username}<br>
                    <strong>Email:</strong> ${order.email}<br>
                    <strong>Phone:</strong> ${order.phone}
                `;
                
                // Update order information
                document.getElementById('modal-order-info').innerHTML = `
                    <strong>Order Number:</strong> #${order.order_number}<br>
                    <strong>Date:</strong> ${new Date(order.created_at).toLocaleString()}<br>
                    <strong>Status:</strong> ${order.status}<br>
                    <strong>Total:</strong> ${order.currency} ${parseFloat(order.total_amount).toFixed(2)}
                `;
                
                // Update order items
                document.getElementById('modal-order-items').innerHTML = order.items;
            });
        });
    </script>
</body>
</html> 