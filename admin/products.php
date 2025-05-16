
<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php'; // Include your database connection

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
$message = '';
$messageType = '';

// Handle Create Product Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $productName = $_POST['product_name'] ?? '';
    $productCategory = $_POST['product_category'] ?? '';
    $productPrice = $_POST['product_price'] ?? '';
    $productStock = $_POST['product_stock'] ?? '';
    $productDescription = $_POST['product_description'] ?? '';
    $productImage = $_FILES['product_image']['name'] ?? '';
    $productBarcode = $_POST['product_barcode'] ?? '';
    $discountType = $_POST['discount_type'] ?? 'none';
    $discountValue = $_POST['discount_value'] ?? 0;

    if (empty($productName) || empty($productCategory) || empty($productPrice) || empty($productBarcode)) {
        $message = 'Please fill in all required fields';
        $messageType = 'error';
    } else {
        // Handle file upload
        if (!empty($productImage)) {
            $targetDir = "../uploads/";
            $targetFile = $targetDir . basename($productImage);
            move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile);
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, description, image_path, barcode, discount_type, discount_value) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdissssd", $productName, $productCategory, $productPrice, $productStock, $productDescription, $targetFile, $productBarcode, $discountType, $discountValue);

        if ($stmt->execute()) { 
            $message = 'Product "' . htmlspecialchars($productName) . '" created successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error creating product';
            $messageType = 'error';
        }

        $stmt->close();
    }
}

// Handle Update Product Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $productId = $_POST['product_id'] ?? '';
    $productName = $_POST['product_name'] ?? '';
    $productCategory = $_POST['product_category'] ?? '';
    $productPrice = $_POST['product_price'] ?? '';
    $productStock = $_POST['product_stock'] ?? '';
    $productDescription = $_POST['product_description'] ?? '';
    $productImage = $_FILES['product_image']['name'] ?? '';
    $productBarcode = $_POST['product_barcode'] ?? '';
    $discountType = $_POST['discount_type'] ?? 'none';
    $discountValue = $_POST['discount_value'] ?? 0;

    if (empty($productId) || empty($productName) || empty($productCategory) || empty($productPrice) || empty($productBarcode)) {
        $message = 'Please fill in all required fields';
        $messageType = 'error';
    } else {
        // Check if a new image was uploaded
        if (!empty($productImage)) {
            $targetDir = "../uploads/";
            $targetFile = $targetDir . basename($productImage);
            move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile);
            
            // Update with new image
            $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, image_path = ?, barcode = ?, discount_type = ?, discount_value = ? WHERE id = ?");
            $stmt->bind_param("ssdisssdi", $productName, $productCategory, $productPrice, $productStock, $productDescription, $targetFile, $productBarcode, $discountType, $discountValue, $productId);
        } else {
            // Update without changing the image
            $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, barcode = ?, discount_type = ?, discount_value = ? WHERE id = ?");
            $stmt->bind_param("ssdissdi", $productName, $productCategory, $productPrice, $productStock, $productDescription, $productBarcode, $discountType, $discountValue, $productId);
        }

        if ($stmt->execute()) {
            $message = 'Product "' . htmlspecialchars($productName) . '" updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating product: ' . $conn->error;
            $messageType = 'error';
        }

        $stmt->close();
    }
}

// Fetch product details for editing if product_id is provided
$editProduct = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $productId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $editProduct = $result->fetch_assoc();
    }
    
    $stmt->close();
}

// Fetch recent products from the database
// Fetch recent products from the database
$recentProductsQuery = "SELECT id, name, category, price, stock, description, image_path, discount_type, discount_value FROM products ORDER BY created_at DESC LIMIT 10";
$recentProductsResult = $conn->query($recentProductsQuery);
// Function to calculate final price after discount
function calculateFinalPrice($price, $discountType, $discountValue) {
    if ($discountType === 'percentage') {
        return $price * (1 - ($discountValue / 100));
    } elseif ($discountType === 'fixed') {
        return max(0, $price - $discountValue);
    }
    return $price;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management | KICKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Import Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Adashboard.css">
    <link rel="stylesheet" href="../assets/css/prod.css">
    <link rel="stylesheet" href="../assets/css/product-management.css">
    <style>
        .product-price .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 8px;
            font-size: 0.9em;
        }
        .product-price .final-price {
            color: #e63946;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-box"></i>
                Product Management
            </h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo substr($adminName, 0, 1); ?>
                </div>
                <span class="user-name"><?php echo $adminName; ?></span>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="sidebar">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <div class="section-header">
                    <h2>Product Management</h2>
                    <p>Create, edit and manage your product inventory</p>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div class="product-form-container">
                    <h3 class="form-title">
                        <?php if ($editProduct): ?>
                            <i class="fas fa-edit"></i> Edit Product: <?php echo htmlspecialchars($editProduct['name']); ?>
                        <?php else: ?>
                            <i class="fas fa-plus-circle"></i> Create New Product
                        <?php endif; ?>
                    </h3>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?php if ($editProduct): ?>
                            <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="product_name">Product Name *</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter product name" required 
                                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['name']) : (isset($productName) ? htmlspecialchars($productName) : ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="product_category">Brand *</label>
                                <input type="text" id="product_category" name="product_category" class="form-control" placeholder="Enter brand name (e.g. Nike, Adidas, New Balance)" required 
                                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['category']) : (isset($productCategory) ? htmlspecialchars($productCategory) : ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="product_price">Price ($) *</label>
                                <input type="number" id="product_price" name="product_price" class="form-control" placeholder="Enter price" step="0.01" min="0" required 
                                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['price']) : (isset($productPrice) ? htmlspecialchars($productPrice) : ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="product_stock">Stock Quantity</label>
                                <input type="number" id="product_stock" name="product_stock" class="form-control" placeholder="Enter stock quantity" min="0" 
                                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['stock']) : (isset($productStock) ? htmlspecialchars($productStock) : ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="product_barcode">Barcode *</label>
                                <input type="text" id="product_barcode" name="product_barcode" class="form-control" placeholder="Enter product barcode" required 
                                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['barcode']) : (isset($productBarcode) ? htmlspecialchars($productBarcode) : ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="discount_type">Discount Type</label>
                                <select id="discount_type" name="discount_type" class="form-control">
                                    <option value="none" <?php echo ($editProduct && $editProduct['discount_type'] == 'none') ? 'selected' : ''; ?>>None</option>
                                    <option value="percentage" <?php echo ($editProduct && $editProduct['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                                    <option value="fixed" <?php echo ($editProduct && $editProduct['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount ($)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="discount_value">Discount Value</label>
                                <input type="number" id="discount_value" name="discount_value" class="form-control" placeholder="Enter discount value" step="0.01" min="0"
                                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['discount_value']) : (isset($discountValue) ? htmlspecialchars($discountValue) : '0'); ?>">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="product_description">Description</label>
                                <textarea id="product_description" name="product_description" class="form-control" placeholder="Enter product description"><?php echo $editProduct ? htmlspecialchars($editProduct['description']) : (isset($productDescription) ? htmlspecialchars($productDescription) : ''); ?></textarea>
                            </div>
                            
                            <div class="form-group full-width">
                                <label>Product Image</label>
                                <?php if ($editProduct && !empty($editProduct['image_path'])): ?>
                                    <div class="current-image">
                                        <p>Current image:</p>
                                        <img src="<?php echo htmlspecialchars($editProduct['image_path']); ?>" alt="Current product image" style="max-width: 100px; max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                                <div class="file-upload">
                                    <label for="product_image" class="upload-btn">
                                        <i class="fas fa-cloud-upload-alt"></i> Choose Image
                                    </label>
                                    <input type="file" id="product_image" name="product_image" accept="image/*">
                                </div>
                                <div id="file-name" class="file-name">No file chosen</div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <?php if ($editProduct): ?>
                                <button type="submit" name="update_product" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Product
                                </button>
                            <?php else: ?>
                                <button type="submit" name="create_product" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Product
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <div class="recent-section">
                    <div class="section-header">
                        <h3 class="section-title">Recent Products</h3>
                    </div>
                    
                    <div class="product-list">
                        <?php if ($recentProductsResult->num_rows > 0): ?>
                            <?php while ($product = $recentProductsResult->fetch_assoc()): ?>
                                <?php
                                $finalPrice = calculateFinalPrice($product['price'], $product['discount_type'], $product['discount_value']);
                                $hasDiscount = $finalPrice < $product['price'];
                                ?>
                                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="product-details">
                                        <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                                        <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                        <div class="product-price-stock">
                                            <?php if ($hasDiscount): ?>
                                                <div class="product-price">
                                                    <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                                    <span class="final-price">$<?php echo number_format($finalPrice, 2); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                            <?php endif; ?>
                                            <div class="product-stock">In Stock: <?php echo $product['stock']; ?></div>
                                        </div>
                                        <div class="product-actions">
                                            <button class="product-action-btn edit" data-id="<?php echo $product['id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="product-action-btn delete" data-id="<?php echo $product['id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No recent products available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="tab-container">
                <div class="tab active" data-tab="edit">Edit Product</div>
                <div class="tab" data-tab="preview">Preview</div>
            </div>
            
            <div id="editTab" class="tab-content">
                <div class="product-preview">
                    <img id="previewImage" src="" alt="Product" class="preview-image">
                    <div class="preview-details">
                        <h4 id="previewName"></h4>
                        <p id="previewCategory"></p>
                        <p>$<span id="previewPrice"></span> - <span id="previewStock"></span> in stock</p>
                    </div>
                </div>
                
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_product_id" name="product_id">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_product_name">Product Name *</label>
                            <input type="text" id="edit_product_name" name="product_name" class="form-control" placeholder="Enter product name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_product_category">Brand *</label>
                            <input type="text" id="edit_product_category" name="product_category" class="form-control" placeholder="Enter brand name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_product_price">Price ($) *</label>
                            <input type="number" id="edit_product_price" name="product_price" class="form-control" placeholder="Enter price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_product_stock">Stock Quantity</label>
                            <input type="number" id="edit_product_stock" name="product_stock" class="form-control" placeholder="Enter stock quantity" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_product_barcode">Barcode *</label>
                            <input type="text" id="edit_product_barcode" name="product_barcode" class="form-control" placeholder="Enter product barcode" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_discount_type">Discount Type</label>
                            <select id="edit_discount_type" name="discount_type" class="form-control">
                                <option value="none">None</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_discount_value">Discount Value</label>
                            <input type="number" id="edit_discount_value" name="discount_value" class="form-control" placeholder="Enter discount value" step="0.01" min="0">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="edit_product_description">Description</label>
                            <textarea id="edit_product_description" name="product_description" class="form-control" placeholder="Enter product description"></textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Product Image</label>
                            <div class="file-upload">
                                <label for="edit_product_image" class="upload-btn">
                                    <i class="fas fa-cloud-upload-alt"></i> Choose Image
                                </label>
                                <input type="file" id="edit_product_image" name="product_image" accept="image/*">
                            </div>
                            <div id="edit_file_name" class="file-name">No file chosen</div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancelEdit">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
            
            <div id="previewTab" class="tab-content" style="display: none;">
                <div class="product-card preview-card">
                    <div class="product-image">
                        <img id="fullPreviewImage" src="" alt="Product preview">
                    </div>
                    <div class="product-details">
                        <h4 id="fullPreviewName"></h4>
                        <div id="fullPreviewCategory"></div>
                        <div class="product-price-stock">
                            <div class="product-price">$<span id="fullPreviewPrice"></span></div>
                            <div class="product-stock">In Stock: <span id="fullPreviewStock"></span></div>
                        </div>
                        <div class="product-description">
                            <p id="fullPreviewDescription"></p>
                        </div>
                        <div class="product-barcode">
                            <strong>Barcode:</strong> <span id="fullPreviewBarcode"></span>
                        </div>
                        <div class="product-discount">
                            <strong>Discount:</strong> <span id="fullPreviewDiscountType"></span> - <span id="fullPreviewDiscountValue"></span>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="backToEdit">
                        <i class="fas fa-arrow-left"></i> Back to Edit
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Dialog -->
    <div id="deleteConfirmDialog" class="confirm-dialog">
        <h3><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h3>
        <p>Are you sure you want to delete "<span id="deleteProductName"></span>"?</p>
        <p class="warning">This action cannot be undone.</p>
        <div class="confirm-actions">
            <button id="cancelDelete" class="confirm-cancel"><i class="fas fa-times"></i> Cancel</button>
            <button id="confirmDelete" class="confirm-delete"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
    
    <!-- Overlay -->
    <div id="overlay" class="overlay"></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add active class to current navigation item
            const currentLocation = location.href;
            const menuItems = document.querySelectorAll('.sidebar a');
            menuItems.forEach(item => {
                if(item.href === currentLocation) {
                    item.classList.add('active');
                }
            });
            
            // Display file name when a file is selected
            const fileInput = document.getElementById('product_image');
            const fileNameDisplay = document.getElementById('file-name');
            
            fileInput.addEventListener('change', function() {
                if(this.files && this.files[0]) {
                    fileNameDisplay.textContent = this.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'No file chosen';
                }
            });
            
            // Edit file input
            const editFileInput = document.getElementById('edit_product_image');
            const editFileNameDisplay = document.getElementById('edit_file_name');
            
            editFileInput.addEventListener('change', function() {
                if(this.files && this.files[0]) {
                    editFileNameDisplay.textContent = this.files[0].name;
                    
                    // Preview the selected image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImage').src = e.target.result;
                        document.getElementById('fullPreviewImage').src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    editFileNameDisplay.textContent = 'No file chosen';
                }
            });
            
            // Animation for product cards
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Modal functionality
            const modal = document.getElementById('editProductModal');
            const closeModal = document.querySelector('.close-modal');
            const cancelEdit = document.getElementById('cancelEdit');
            
            // Close modal when clicking the X
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close modal when clicking Cancel
            cancelEdit.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close modal when clicking outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Tab switching in modal
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Show corresponding tab content
                    const tabName = this.getAttribute('data-tab');
                    if (tabName === 'edit') {
                        document.getElementById('editTab').style.display = 'block';
                        document.getElementById('previewTab').style.display = 'none';
                    } else if (tabName === 'preview') {
                        document.getElementById('editTab').style.display = 'none';
                        document.getElementById('previewTab').style.display = 'block';
                        
                        // Update preview with current form values
                        updatePreview();
                    }
                });
            });
            
            // Back to edit button
            document.getElementById('backToEdit').addEventListener('click', function() {
                document.querySelector('.tab[data-tab="edit"]').click();
            });
            
            // Function to update preview
            function updatePreview() {
                const name = document.getElementById('edit_product_name').value;
                const category = document.getElementById('edit_product_category').value;
                const price = document.getElementById('edit_product_price').value;
                const stock = document.getElementById('edit_product_stock').value;
                const description = document.getElementById('edit_product_description').value;
                const barcode = document.getElementById('edit_product_barcode').value;
                const discountType = document.getElementById('edit_discount_type').value;
                const discountValue = document.getElementById('edit_discount_value').value;
                
                document.getElementById('fullPreviewName').textContent = name;
                document.getElementById('fullPreviewCategory').textContent = category;
                document.getElementById('fullPreviewPrice').textContent = parseFloat(price).toFixed(2);
                document.getElementById('fullPreviewStock').textContent = stock;
                document.getElementById('fullPreviewDescription').textContent = description;
                document.getElementById('fullPreviewBarcode').textContent = barcode;
                
                // Format discount display
                let discountTypeText = 'None';
                let discountValueText = '';
                
                if (discountType === 'percentage') {
                    discountTypeText = 'Percentage';
                    discountValueText = discountValue + '%';
                } else if (discountType === 'fixed') {
                    discountTypeText = 'Fixed Amount';
                    discountValueText = '$' + parseFloat(discountValue).toFixed(2);
                }
                
                document.getElementById('fullPreviewDiscountType').textContent = discountTypeText;
                document.getElementById('fullPreviewDiscountValue').textContent = discountValueText;
            }
            
            // Edit product functionality
            const editButtons = document.querySelectorAll('.product-action-btn.edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    
                    // Fetch product data via AJAX
                    fetch(`get_product.php?id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                const product = data.product;
                                
                                // Populate the edit form
                                document.getElementById('edit_product_id').value = product.id;
                                document.getElementById('edit_product_name').value = product.name;
                                document.getElementById('edit_product_category').value = product.category;
                                document.getElementById('edit_product_price').value = product.price;
                                document.getElementById('edit_product_stock').value = product.stock;
                                document.getElementById('edit_product_description').value = product.description;
                                document.getElementById('edit_product_barcode').value = product.barcode;
                                document.getElementById('edit_discount_type').value = product.discount_type || 'none';
                                document.getElementById('edit_discount_value').value = product.discount_value || '0';
                                
                                // Update preview image
                                document.getElementById('previewImage').src = product.image_path;
                                document.getElementById('fullPreviewImage').src = product.image_path;
                                
                                // Update preview text
                                document.getElementById('previewName').textContent = product.name;
                                document.getElementById('previewCategory').textContent = product.category;
                                document.getElementById('previewPrice').textContent = parseFloat(product.price).toFixed(2);
                                document.getElementById('previewStock').textContent = product.stock;
                                
                                // Show the modal
                                modal.style.display = 'block';
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching product data:', error);
                            alert('Failed to load product data. Please try again.');
                        });
                });
            });
            
            // Handle edit form submission
            document.getElementById('editProductForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                // Send AJAX request to update product
                fetch('edit_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        // Reload the page to show updated product
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating product:', error);
                    alert('An error occurred while updating the product. Please try again.');
                });
            });
            
            // Delete product functionality
            const deleteButtons = document.querySelectorAll('.product-action-btn.delete');
            const deleteConfirmDialog = document.getElementById('deleteConfirmDialog');
            const overlay = document.getElementById('overlay');
            const deleteProductName = document.getElementById('deleteProductName');
            const cancelDeleteBtn = document.getElementById('cancelDelete');
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            let currentProductId = null;
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    currentProductId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    
                    // Set product name in confirmation dialog
                    deleteProductName.textContent = productName;
                    
                    // Show confirmation dialog and overlay
                    deleteConfirmDialog.style.display = 'block';
                    overlay.style.display = 'block';
                });
            });
            
            // Cancel delete
            cancelDeleteBtn.addEventListener('click', function() {
                deleteConfirmDialog.style.display = 'none';
                overlay.style.display = 'none';
                currentProductId = null;
            });
            
            // Confirm delete
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentProductId) {
                    const formData = new FormData();
                    formData.append('product_id', currentProductId);
                    
                    // Send AJAX request to delete product
                    fetch('delete_product.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            // Remove the product card from the DOM
                            const productCard = document.querySelector(`.product-card[data-product-id="${currentProductId}"]`);
                            if (productCard) {
                                productCard.remove();
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                        
                        // Hide confirmation dialog and overlay
                        deleteConfirmDialog.style.display = 'none';
                        overlay.style.display = 'none';
                        currentProductId = null;
                    })
                    .catch(error => {
                        console.error('Error deleting product:', error);
                        alert('An error occurred while deleting the product. Please try again.');
                        
                        // Hide confirmation dialog and overlay
                        deleteConfirmDialog.style.display = 'none';
                        overlay.style.display = 'none';
                        currentProductId = null;
                    });
                }
            });
            
            // Close confirmation dialog when clicking outside
            overlay.addEventListener('click', function() {
                deleteConfirmDialog.style.display = 'none';
                overlay.style.display = 'none';
                currentProductId = null;
            });
        });
    </script>

    

</body>
</html>
</qodoArtifact>
