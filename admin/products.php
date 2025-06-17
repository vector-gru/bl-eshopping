<?php
require_once '../auth/admin_auth.php';
requireAdmin();

require_once '../database/db_connect.php';
$conn = getDBConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $item_id = $_POST['action'] === 'edit' ? $_POST['item_id'] : null;
                $item_brand = $_POST['item_brand'];
                $item_name = $_POST['item_name'];
                $item_price = $_POST['item_price'];
                $currency = $_POST['currency'];
                $item_description = $_POST['item_description'];
                $stock_quantity = $_POST['stock_quantity'];
                $is_active = isset($_POST['is_active']) ? 1 : 0;

                try {
                    $conn->beginTransaction();

                    if ($_POST['action'] === 'add') {
                        $stmt = $conn->prepare("INSERT INTO product (item_brand, item_name, item_price, currency, item_description, stock_quantity, is_active) 
                                              VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$item_brand, $item_name, $item_price, $currency, $item_description, $stock_quantity, $is_active]);
                        $item_id = $conn->lastInsertId();
                    } else {
                        $stmt = $conn->prepare("UPDATE product SET item_brand = ?, item_name = ?, item_price = ?, 
                                              currency = ?, item_description = ?, stock_quantity = ?, is_active = ? 
                                              WHERE item_id = ?");
                        $stmt->execute([$item_brand, $item_name, $item_price, $currency, $item_description, $stock_quantity, $is_active, $item_id]);
                    }

                    // Handle image uploads
                    if (!empty($_FILES['images']['name'][0])) {
                        $upload_dir = '../assets/products/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        // Delete old images if editing
                        if ($_POST['action'] === 'edit') {
                            $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE item_id = ?");
                            $stmt->execute([$item_id]);
                            $old_images = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            foreach ($old_images as $old_image) {
                                if (file_exists('../' . $old_image)) {
                                    unlink('../' . $old_image);
                                }
                            }
                            $stmt = $conn->prepare("DELETE FROM product_images WHERE item_id = ?");
                            $stmt->execute([$item_id]);
                        }

                        // Upload new images
                        $stmt = $conn->prepare("INSERT INTO product_images (item_id, image_path, is_primary) VALUES (?, ?, ?)");
                        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                            $file_name = uniqid() . '_' . $_FILES['images']['name'][$key];
                            $file_path = 'assets/products/' . $file_name;
                            
                            if (move_uploaded_file($tmp_name, $upload_dir . $file_name)) {
                                $is_primary = $key === 0 ? 1 : 0; // First image is primary
                                $stmt->execute([$item_id, $file_path, $is_primary]);
                            }
                        }

                        // Update main product image
                        $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE item_id = ? AND is_primary = 1");
                        $stmt->execute([$item_id]);
                        $primary_image = $stmt->fetchColumn();
                        
                        if ($primary_image) {
                            $stmt = $conn->prepare("UPDATE product SET item_image = ? WHERE item_id = ?");
                            $stmt->execute([$primary_image, $item_id]);
                        }
                    }

                    $conn->commit();
                    header('Location: products.php?success=1');
                    exit;
                } catch (Exception $e) {
                    $conn->rollBack();
                    $error = "Error: " . $e->getMessage();
                }
                break;

            case 'delete':
                if (isset($_POST['item_id'])) {
                    try {
                        $conn->beginTransaction();
                        
                        // Delete product images
                        $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE item_id = ?");
                        $stmt->execute([$_POST['item_id']]);
                        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        foreach ($images as $image) {
                            if (file_exists('../' . $image)) {
                                unlink('../' . $image);
                            }
                        }
                        
                        // Delete product
                        $stmt = $conn->prepare("DELETE FROM product WHERE item_id = ?");
                        $stmt->execute([$_POST['item_id']]);
                        
                        $conn->commit();
                        header('Location: products.php?success=2');
                        exit;
                    } catch (Exception $e) {
                        $conn->rollBack();
                        $error = "Error: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get all products
$stmt = $conn->query("SELECT p.*, 
                     (SELECT COUNT(*) FROM product_images WHERE item_id = p.item_id) as image_count 
                     FROM product p ORDER BY p.item_register DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
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
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
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
                            <a class="nav-link active" href="products.php">
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
                            <a class="nav-link text-danger" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Products</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Add New Product
                    </button>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        switch ($_GET['success']) {
                            case 1:
                                echo "Product saved successfully!";
                                break;
                            case 2:
                                echo "Product deleted successfully!";
                                break;
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Images</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($product['item_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['item_name']); ?>"
                                                     class="product-image rounded">
                                            </td>
                                            <td><?php echo htmlspecialchars($product['item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['item_brand']); ?></td>
                                            <td>
                                                <?php echo $product['currency'] . ' ' . number_format($product['item_price'], 2); ?>
                                            </td>
                                            <td><?php echo $product['stock_quantity']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo $product['image_count']; ?> images
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-product" 
                                                        data-product='<?php echo htmlspecialchars(json_encode($product)); ?>'
                                                        data-bs-toggle="modal" data-bs-target="#editProductModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-product"
                                                        data-item-id="<?php echo $product['item_id']; ?>"
                                                        data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                                                    <i class="fas fa-trash"></i>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="item_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="item_brand" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="item_price" step="0.01" required>
                                    <select class="form-select" name="currency" style="max-width: 100px;">
                                        <option value="XAF">XAF</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_quantity" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="item_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Images</label>
                            <input type="file" class="form-control" name="images[]" multiple accept="image/*" required>
                            <small class="text-muted">First image will be used as the main product image</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="item_id" id="edit_item_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="item_name" id="edit_item_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="item_brand" id="edit_item_brand" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="item_price" id="edit_item_price" step="0.01" required>
                                    <select class="form-select" name="currency" id="edit_currency" style="max-width: 100px;">
                                        <option value="XAF">XAF</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_quantity" id="edit_stock_quantity" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="item_description" id="edit_item_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Images</label>
                            <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                            <small class="text-muted">Leave empty to keep existing images. First image will be used as the main product image</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="item_id" id="delete_item_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit product
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const product = JSON.parse(this.dataset.product);
                document.getElementById('edit_item_id').value = product.item_id;
                document.getElementById('edit_item_name').value = product.item_name;
                document.getElementById('edit_item_brand').value = product.item_brand;
                document.getElementById('edit_item_price').value = product.item_price;
                document.getElementById('edit_currency').value = product.currency;
                document.getElementById('edit_stock_quantity').value = product.stock_quantity;
                document.getElementById('edit_item_description').value = product.item_description;
                document.getElementById('edit_is_active').checked = product.is_active == 1;
            });
        });

        // Handle delete product
        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete_item_id').value = this.dataset.itemId;
            });
        });
    </script>
</body>
</html> 