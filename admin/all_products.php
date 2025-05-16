<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php'; // Include your database connection

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';

// Fetch all products from the database
$allProductsQuery = "SELECT id, name, category, price, stock, description, image_path, discount_type, discount_value FROM products ORDER BY created_at DESC";
$allProductsResult = $conn->query($allProductsQuery);

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
    <title>All Products | KICKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/Adashboard.css">
    <link rel="stylesheet" href="../assets/css/prod.css">
    <link rel="stylesheet" href="../assets/css/product-management.css">
    <style>
        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-card {
            width: calc(33.333% - 20px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
            background-color: #fff;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        .product-details {
            padding: 15px;
        }
        .product-name {
            font-size: 1.2em;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }
        .product-category {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .product-price-stock {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .product-price {
            font-weight: bold;
            color: #e63946;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 8px;
            font-size: 0.9em;
        }
        .final-price {
            font-size: 1.1em;
        }
        .product-stock {
            font-size: 0.9em;
            color: #999;
            background-color: #f8f9fa;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .product-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.2s;
            flex: 1;
            text-align: center;
            margin: 0 5px;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        .action-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            align-items: center;
        }
        .search-container {
            display: flex;
            max-width: 400px;
            width: 100%;
        }
        .search-container input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 14px;
        }
        .search-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
        }
        .filter-container select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-box"></i>
                All Products
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
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="all_products.php" class="active"><i class="fas fa-box-open"></i> All Products</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="../auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="main-content">
                <div class="section-header">
                    <h2>All Products</h2>
                    <p>View and manage all products in your inventory</p>
                </div>
                
                <div class="action-bar">
                    <div class="search-container">
                        <input type="text" id="product-search" placeholder="Search products...">
                        <button type="button"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="filter-container">
                        <select id="category-filter">
                            <option value="">All Categories</option>
                            <option value="Sneakers">Sneakers</option>
                            <option value="Running">Running</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Casual">Casual</option>
                        </select>
                    </div>
                </div>
                
                <div class="product-list">
                    <?php if ($allProductsResult->num_rows > 0): ?>
                        <?php while ($product = $allProductsResult->fetch_assoc()): ?>
                            <?php
                            $finalPrice = calculateFinalPrice($product['price'], $product['discount_type'], $product['discount_value']);
                            $hasDiscount = $finalPrice < $product['price'];
                            ?>
                            <div class="product-card" data-product-id="<?php echo $product['id']; ?>" data-category="<?php echo htmlspecialchars($product['category']); ?>">
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
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="javascript:void(0);" class="action-btn delete-btn" data-id="<?php echo $product['id']; ?>"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No products available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Search functionality
        document.getElementById('product-search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const productName = product.querySelector('.product-name').textContent.toLowerCase();
                const productCategory = product.querySelector('.product-category').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
        
        // Category filter
        document.getElementById('category-filter').addEventListener('change', function() {
            const selectedCategory = this.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const productCategory = product.dataset.category.toLowerCase();
                
                if (selectedCategory === '' || productCategory === selectedCategory) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
        
        // Delete product confirmation
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const confirmDelete = confirm('Are you sure you want to delete this product?');
                
                if (confirmDelete) {
                    // Send AJAX request to delete product
                    fetch('delete_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + productId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove product card from DOM
                            this.closest('.product-card').remove();
                            alert('Product deleted successfully');
                        } else {
                            alert('Error deleting product: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the product');
                    });
                }
            });
        });
    </script>
</body>
</html>
