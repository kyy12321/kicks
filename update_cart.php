<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantityChange = filter_input(INPUT_POST, 'quantity_change', FILTER_VALIDATE_INT);

    if (!$productId || !$quantityChange) {
        throw new Exception('Invalid request parameters');
    }

    // Get product details
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        throw new Exception('Product not found');
    }

    // Update cart quantity
    $newQuantity = $_SESSION['cart'][$productId]['quantity'] + $quantityChange;

    if ($newQuantity < 1) {
        throw new Exception('Quantity cannot be less than 1');
    }

    if ($newQuantity > $product['stock']) {
        throw new Exception('Cannot add more than available stock');
    }

    $_SESSION['cart'][$productId]['quantity'] = $newQuantity;

    echo json_encode(['success' => true, 'message' => 'Cart updated']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}