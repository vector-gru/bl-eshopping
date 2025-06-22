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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $conn->beginTransaction();
                    
                    // Insert product
                    $stmt = $conn->prepare("INSERT INTO product (item_name, item_price, old_price, item_description, item_brand, currency, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['item_name'],
                        $_POST['item_price'],
                        $_POST['old_price'] ?: null,
                        $_POST['item_description'],
                        $_POST['item_brand'],
                        $_POST['currency'],
                        $_POST['stock_quantity']
                    ]);
                    
                    $product_id = $conn->lastInsertId();
                    
                    // Handle image uploads
                    if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                        $upload_dir = '../assets/uploads/products/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $image_count = count($_FILES['product_images']['name']);
                        
                        for ($i = 0; $i < $image_count; $i++) {
                            if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                                $file_name = $_FILES['product_images']['name'][$i];
                                $file_tmp = $_FILES['product_images']['tmp_name'][$i];
                                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                                
                                // Generate unique filename
                                $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
                                $upload_path = $upload_dir . $unique_name;
                                
                                // Check if file is an image
                                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                if (in_array($file_ext, $allowed_extensions) && move_uploaded_file($file_tmp, $upload_path)) {
                                    // Insert image record
                                    $is_primary = ($i === 0) ? 1 : 0; // First image is primary
                                    $stmt = $conn->prepare("INSERT INTO product_images (item_id, image_path, image_name, is_primary, sort_order) VALUES (?, ?, ?, ?, ?)");
                                    $stmt->execute([
                                        $product_id,
                                        'assets/uploads/products/' . $unique_name,
                                        $file_name,
                                        $is_primary,
                                        $i
                                    ]);
                                }
                            }
                        }
                    }
                    
                    $conn->commit();
                    $message = "Product added successfully!";
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $message = "Error adding product: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    $conn->beginTransaction();
                    
                    // Update product
                    $stmt = $conn->prepare("UPDATE product SET item_name = ?, item_price = ?, old_price = ?, item_description = ?, item_brand = ?, currency = ?, stock_quantity = ? WHERE item_id = ?");
                    $stmt->execute([
                        $_POST['item_name'],
                        $_POST['item_price'],
                        $_POST['old_price'] ?: null,
                        $_POST['item_description'],
                        $_POST['item_brand'],
                        $_POST['currency'],
                        $_POST['stock_quantity'],
                        $_POST['product_id']
                    ]);
                    
                    // Handle new image uploads
                    if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                        $upload_dir = '../assets/uploads/products/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $image_count = count($_FILES['product_images']['name']);
                        
                        for ($i = 0; $i < $image_count; $i++) {
                            if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                                $file_name = $_FILES['product_images']['name'][$i];
                                $file_tmp = $_FILES['product_images']['tmp_name'][$i];
                                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                                
                                // Generate unique filename
                                $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
                                $upload_path = $upload_dir . $unique_name;
                                
                                // Check if file is an image
                                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                if (in_array($file_ext, $allowed_extensions) && move_uploaded_file($file_tmp, $upload_path)) {
                                    // Insert image record
                                    $stmt = $conn->prepare("INSERT INTO product_images (item_id, image_path, image_name, is_primary, sort_order) VALUES (?, ?, ?, ?, ?)");
                                    $stmt->execute([
                                        $_POST['product_id'],
                                        'assets/uploads/products/' . $unique_name,
                                        $file_name,
                                        0, // Not primary for additional images
                                        $i
                                    ]);
                                }
                            }
                        }
                    }
                    
                    $conn->commit();
                    $message = "Product updated successfully!";
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $message = "Error updating product: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $conn->beginTransaction();
                    
                    // Get product images to delete files
                    $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE item_id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Delete image files
                    foreach ($images as $image) {
                        $file_path = '../' . $image['image_path'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    
                    // Delete product (cascades to product_images)
                    $stmt = $conn->prepare("DELETE FROM product WHERE item_id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    
                    $conn->commit();
                    $message = "Product deleted successfully!";
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $message = "Error deleting product: " . $e->getMessage();
                }
                break;
                
            case 'delete_image':
                try {
                    $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE id = ?");
                    $stmt->execute([$_POST['image_id']]);
                    $image = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($image) {
                        // Delete file
                        $file_path = '../' . $image['image_path'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        
                        // Delete from database
                        $stmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
                        $stmt->execute([$_POST['image_id']]);
                        
                        $message = "Image deleted successfully!";
                    }
                } catch (PDOException $e) {
                    $message = "Error deleting image: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get all products with image count
$products = $conn->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM product_images WHERE item_id = p.item_id) as image_count,
           (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
    FROM product p 
    ORDER BY p.item_register DESC
")->fetchAll(PDO::FETCH_ASSOC);
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
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
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
                    <h2>Manage Products</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Add New Product
                    </button>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $index => $product): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <?php if ($product['primary_image']): ?>
                                                <img src="<?php echo '../' . htmlspecialchars($product['primary_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['item_name']); ?>"
                                                     class="product-image rounded">
                                            <?php else: ?>
                                                <div class="product-image rounded bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['item_brand']); ?></td>
                                        <td>
                                            <?php if ($product['old_price']): ?>
                                                <span class="text-muted text-decoration-line-through"><?php echo $product['currency'] . ' ' . number_format($product['old_price'], 2); ?></span><br>
                                            <?php endif; ?>
                                            <span class="text-danger fw-bold"><?php echo $product['currency'] . ' ' . number_format($product['item_price'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $product['stock_quantity']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-product" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editProductModal"
                                                    data-product='<?php echo json_encode($product); ?>'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="product_id" value="<?php echo $product['item_id']; ?>">
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" name="item_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Brand *</label>
                                    <input type="text" class="form-control" name="item_brand" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Price *</label>
                                    <div class="input-group">
                                        <select class="form-select" name="currency" style="max-width: 100px;">
                                            <option value="XAF">XAF</option>
                                            <option value="USD">USD</option>
                                        </select>
                                        <input type="number" class="form-control" name="item_price" step="0.01" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Old Price (Optional)</label>
                                    <input type="number" class="form-control" name="old_price" step="0.01">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" name="stock_quantity" value="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Images *</label>
                                    <input type="file" class="form-control" name="product_images[]" multiple accept="image/*" required>
                                    <small class="text-muted">You can select multiple images. The first image will be the primary image.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea class="form-control" name="item_description" rows="8" required></textarea>
                                </div>
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
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" name="item_name" id="edit_item_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Brand *</label>
                                    <input type="text" class="form-control" name="item_brand" id="edit_item_brand" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Price *</label>
                                    <div class="input-group">
                                        <select class="form-select" name="currency" id="edit_currency" style="max-width: 100px;">
                                            <option value="XAF">XAF</option>
                                            <option value="USD">USD</option>
                                        </select>
                                        <input type="number" class="form-control" name="item_price" id="edit_item_price" step="0.01" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Old Price (Optional)</label>
                                    <input type="number" class="form-control" name="old_price" id="edit_old_price" step="0.01">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" name="stock_quantity" id="edit_stock_quantity" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Add More Images (Optional)</label>
                                    <input type="file" class="form-control" name="product_images[]" multiple accept="image/*">
                                    <small class="text-muted">Select additional images to add to the product.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea class="form-control" name="item_description" id="edit_item_description" rows="8" required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Images -->
                        <div class="mb-3">
                            <label class="form-label">Current Images</label>
                            <div id="current-images-container" class="image-preview-container">
                                <!-- Images will be loaded here via JavaScript -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit product modal
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const product = JSON.parse(this.dataset.product);
                document.getElementById('edit_product_id').value = product.item_id;
                document.getElementById('edit_item_name').value = product.item_name;
                document.getElementById('edit_item_brand').value = product.item_brand;
                document.getElementById('edit_item_price').value = product.item_price;
                document.getElementById('edit_old_price').value = product.old_price || '';
                document.getElementById('edit_item_description').value = product.item_description;
                document.getElementById('edit_stock_quantity').value = product.stock_quantity;
                document.getElementById('edit_currency').value = product.currency;
                
                // Load current images
                loadProductImages(product.item_id);
            });
        });
        
        // Load product images for edit modal
        function loadProductImages(productId) {
            fetch(`get_product_images.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('current-images-container');
                    container.innerHTML = '';
                    
                    data.forEach(image => {
                        const imageDiv = document.createElement('div');
                        imageDiv.className = 'position-relative';
                        imageDiv.innerHTML = `
                            <img src="../${image.image_path}" alt="${image.image_name}" class="image-preview">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                    onclick="deleteImage(${image.id})" style="margin: 2px;">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        container.appendChild(imageDiv);
                    });
                })
                .catch(error => console.error('Error loading images:', error));
        }
        
        // Delete image
        function deleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image?')) {
                const formData = new FormData();
                formData.append('action', 'delete_image');
                formData.append('image_id', imageId);
                
                fetch('products.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(() => {
                    // Reload the page to refresh the image list
                    location.reload();
                })
                .catch(error => console.error('Error deleting image:', error));
            }
        }
    </script>
</body>
</html> 