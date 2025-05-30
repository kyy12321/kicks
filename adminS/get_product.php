<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Verify authentication
if (!isLoggedIn() || $_SESSION['role'] !== 'superadmin') {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit(json_encode(['status' => 'error', 'message' => 'Invalid product ID']));
}

$productId = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('HTTP/1.1 404 Not Found');
        exit(json_encode(['status' => 'error', 'message' => 'Product not found']));
    }
    
    $product = $result->fetch_assoc();
    
    // Convert numeric values to proper types
    $product['price'] = (float)$product['price'];
    $product['stock'] = (int)$product['stock'];
    $product['discount_value'] = (float)$product['discount_value'];
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'product' => $product
    ]);
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    exit(json_encode([
        'status' => 'error', 
        'message' => 'Failed to retrieve product data'
    ]));
}