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
    // Get product ID
    $productId = $_POST['product_id'] ?? '';
    
    if (empty($productId)) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
        exit();
    }
    
    // Get the image path before deleting the product
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $imagePath = $product['image_path'];
        
        // Delete the product from the database
        $deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $deleteStmt->bind_param("i", $productId);
        
        if ($deleteStmt->execute()) {
            // Optionally delete the image file
            if (!empty($imagePath) && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $conn->error]);
        }
        
        $deleteStmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>