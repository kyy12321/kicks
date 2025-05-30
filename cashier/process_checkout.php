<?php
session_start();
include '../includes/db_connect.php';
include '../includes/auth.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'cashier') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Add debug logging
error_log("Checkout data received: " . print_r($data, true));

try {
    $conn->begin_transaction();
    
    // Calculate totals
    $subtotal = array_reduce($data['items'], fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
    $tax = $subtotal * 0.02;
    $total = $subtotal + $tax;

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id, order_date, subtotal, tax, total, payment_method, status) 
        VALUES (?, NOW(), ?, ?, ?, ?, 'Completed')");
    
    $stmt->bind_param("iddds", 
        $_SESSION['user_id'],
        $subtotal,
        $tax,
        $total,
        $data['paymentMethod']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Order insert failed: " . $stmt->error);
    }
    
    $order_id = $conn->insert_id;
    error_log("New order created: " . $order_id);

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items 
        (order_id, product_id, quantity, price) 
        VALUES (?, ?, ?, ?)");
    
    foreach ($data['items'] as $item) {
        $stmt->bind_param("iiid", 
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Order item insert failed: " . $stmt->error);
        }
        
        // Update product stock
        $updateStock = $conn->query("UPDATE products SET stock = stock - {$item['quantity']} 
            WHERE id = {$item['product_id']}");
        
        if (!$updateStock) {
            throw new Exception("Stock update failed: " . $conn->error);
        }
    }

    $conn->commit();
    error_log("Checkout successful for order: " . $order_id);
    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Checkout error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
?>