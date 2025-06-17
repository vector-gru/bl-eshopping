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

// Handle user status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_status':
            try {
                $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                $stmt->execute([$_POST['is_active'], $_POST['user_id']]);
                $message = "User status updated successfully!";
            } catch (PDOException $e) {
                $message = "Error updating user status: " . $e->getMessage();
            }
            break;

        case 'delete':
            try {
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$_POST['user_id']]);
                $message = "User deleted successfully!";
            } catch (PDOException $e) {
                $message = "Error deleting user: " . $e->getMessage();
            }
            break;
    }
}

// Get all users with their order counts
$stmt = $conn->query("
    SELECT u.*, 
           COUNT(DISTINCT o.id) as total_orders,
           SUM(o.total_amount) as total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
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
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">
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
                    <h2>Manage Users</h2>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Orders</th>
                                        <th>Total Spent</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $index => $user): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                        <td><?php echo $user['total_orders']; ?></td>
                                        <td>
                                            <?php 
                                            if ($user['total_spent']) {
                                                echo 'XAF ' . number_format($user['total_spent'], 2);
                                            } else {
                                                echo 'XAF 0.00';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="is_active" value="<?php echo $user['is_active'] ? '0' : '1'; ?>">
                                                <button type="submit" class="btn btn-sm btn-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#userDetailsModal"
                                                    data-user='<?php echo htmlspecialchars(json_encode($user)); ?>'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>User Information</h6>
                            <p id="modal-user-info"></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Statistics</h6>
                            <p id="modal-user-stats"></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Recent Orders</h6>
                        <div id="modal-user-orders"></div>
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
        // Handle user details modal
        document.querySelectorAll('[data-bs-target="#userDetailsModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const user = JSON.parse(this.dataset.user);
                
                // Update user information
                document.getElementById('modal-user-info').innerHTML = `
                    <strong>Username:</strong> ${user.username}<br>
                    <strong>Email:</strong> ${user.email}<br>
                    <strong>Phone:</strong> ${user.phone}<br>
                    <strong>Status:</strong> ${user.is_active ? 'Active' : 'Inactive'}<br>
                    <strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}
                `;
                
                // Update user statistics
                document.getElementById('modal-user-stats').innerHTML = `
                    <strong>Total Orders:</strong> ${user.total_orders}<br>
                    <strong>Total Spent:</strong> RWF ${parseFloat(user.total_spent || 0).toFixed(2)}
                `;
                
                // Fetch and display recent orders
                fetch(`get_user_orders.php?user_id=${user.id}`)
                    .then(response => response.json())
                    .then(orders => {
                        if (orders.length > 0) {
                            const ordersHtml = orders.map(order => `
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>Order #${order.id}</strong><br>
                                                <small class="text-muted">${new Date(order.created_at).toLocaleString()}</small>
                                            </div>
                                            <div>
                                                <span class="badge bg-${order.status === 'completed' ? 'success' : 'warning'}">
                                                    ${order.status}
                                                </span>
                                                <div class="text-end">
                                                    RWF ${parseFloat(order.total_amount).toFixed(2)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            document.getElementById('modal-user-orders').innerHTML = ordersHtml;
                        } else {
                            document.getElementById('modal-user-orders').innerHTML = '<p class="text-muted">No orders found</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching orders:', error);
                        document.getElementById('modal-user-orders').innerHTML = '<p class="text-danger">Error loading orders</p>';
                    });
            });
        });
    </script>
</body>
</html> 