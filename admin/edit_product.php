<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get product data
    $productId = $_POST['product_id'] ?? '';
    $productName = $_POST['product_name'] ?? '';
    $productCategory = $_POST['product_category'] ?? '';
    $productPrice = $_POST['product_price'] ?? '';
    $productStock = $_POST['product_stock'] ?? '';
    $productDescription = $_POST['product_description'] ?? '';
    
    // Validate required fields
    if (empty($productId) || empty($productName) || empty($productCategory) || empty($productPrice)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields']);
        exit();
    }
    
    // Handle image upload if a new image is provided
    $imagePath = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($_FILES['product_image']['name']);
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
            exit();
        }
    }
    
    // Update product in database
    if ($imagePath) {
        // If a new image was uploaded
        $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sssissi", $productName, $productCategory, $productPrice, $productStock, $productDescription, $imagePath, $productId);
    } else {
        // If no new image was uploaded
        $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $productName, $productCategory, $productPrice, $productStock, $productDescription, $productId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating product: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>