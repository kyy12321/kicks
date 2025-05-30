<?php
session_start();
require 'includes/db_connect.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please login to add items to cart');
    }

    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    
    if (!$productId) {
        throw new Exception('Invalid product');
    }

    // Get product details including stock quantity
    $stmt = $conn->prepare("
        SELECT id, name, price, stock 
        FROM products
        WHERE id = ?
    ");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Product not found');
    }
    
    $product = $result->fetch_assoc();

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Use product ID as cart key
    $cartKey = $productId;

    // Add product to cart or increment quantity
    if (isset($_SESSION['cart'][$cartKey])) {
        // Check stock before increasing quantity
        if ($_SESSION['cart'][$cartKey]['quantity'] >= $product['stock']) {
            throw new Exception('Maximum stock quantity reached');
        }
        $_SESSION['cart'][$cartKey]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$cartKey] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
            'max_quantity' => $product['stock']
        ];
    }

    // Calculate total cart count
    $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

    echo json_encode([
        'success' => true,
        'cart_count' => $cartCount,
        'product_name' => $product['name'],
        'message' => 'Product added to cart'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>