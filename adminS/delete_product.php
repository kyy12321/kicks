<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Verify authentication
if (!isLoggedIn() || $_SESSION['role'] !== 'superadmin') {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
}

try {
    // Validate product ID
    if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit(json_encode(['status' => 'error', 'message' => 'Invalid product ID']));
    }

    $productId = (int)$_POST['product_id'];

    // Get existing image path
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('HTTP/1.1 404 Not Found');
        exit(json_encode(['status' => 'error', 'message' => 'Product not found']));
    }

    $product = $result->fetch_assoc();
    $imagePath = $product['image_path'];

    // Delete product
    $deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $deleteStmt->bind_param("i", $productId);

    if (!$deleteStmt->execute()) {
        throw new Exception('Database deletion failed: ' . $conn->error);
    }

    // Delete associated image
    if ($imagePath && file_exists($imagePath)) {
        if (!unlink($imagePath)) {
            error_log("Warning: Failed to delete image file: $imagePath");
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Product deleted successfully',
        'deleted_id' => $productId
    ]);

} catch (Exception $e) {
    error_log("Delete error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'status' => 'error', 
        'message' => 'Product deletion failed: ' . $e->getMessage()
    ]);
}