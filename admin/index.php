<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug information
error_log("Accessing admin panel");
error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));

// Include required files
require_once __DIR__ . '/../auth/admin_auth.php';

// Debug admin status
try {
    $isAdmin = isAdmin();
    error_log("Admin status check: " . ($isAdmin ? 'true' : 'false'));
} catch (Exception $e) {
    error_log("Error checking admin status: " . $e->getMessage());
    die("Error checking admin status. Please check the error logs.");
}

// Require admin access
requireAdmin();

require_once '../database/db_connect.php';
$conn = getDBConnection();

// Get product count
$stmt = $conn->query("SELECT COUNT(*) as count FROM product");
$productCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get user count
$stmt = $conn->query("SELECT COUNT(*) as count FROM users");
$userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get order count
$stmt = $conn->query("SELECT COUNT(*) as count FROM orders");
$orderCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BL E-Shopping</title>
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
        .stat-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
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
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-box me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
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
                    <h2 class="mb-0">Dashboard</h2>
                    <a href="../index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Home
                    </a>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <h2 class="card-text"><?php echo $productCount; ?></h2>
                                <a href="products.php" class="text-white">Manage Products <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="card-text"><?php echo $userCount; ?></h2>
                                <a href="users.php" class="text-white">Manage Users <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2 class="card-text"><?php echo $orderCount; ?></h2>
                                <a href="orders.php" class="text-white">Manage Orders <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Orders</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $conn->query("SELECT o.*, u.username 
                                                    FROM orders o 
                                                    JOIN users u ON o.user_id = u.id 
                                                    ORDER BY o.created_at DESC 
                                                    LIMIT 5");
                                $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($recentOrders) > 0):
                                    foreach ($recentOrders as $order):
                                ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0">Order #<?php echo $order['order_number']; ?></h6>
                                            <small class="text-muted">By <?php echo htmlspecialchars($order['username']); ?></small>
                                        </div>
                                        <span class="badge bg-<?php echo $order['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <p class="text-muted">No recent orders</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Products</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $conn->query("SELECT p.*, 
                                    (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
                                    FROM product p 
                                    ORDER BY p.item_register DESC LIMIT 5");
                                $recentProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($recentProducts) > 0):
                                    foreach ($recentProducts as $product):
                                        // Set default image if no primary image exists
                                        $imagePath = $product['primary_image'] ? '../' . $product['primary_image'] : '../assets/products/1.png';
                                ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                             alt="<?php echo htmlspecialchars($product['item_name']); ?>"
                                             class="rounded" style="width: 50px; height: 50px; object-fit: cover;"
                                             onerror="this.src='../assets/products/1.png'">
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($product['item_name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo $product['currency'] . ' ' . number_format($product['item_price'], 2); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <p class="text-muted">No products added yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 