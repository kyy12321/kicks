<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to place an order.']);
    exit();
}

if (empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit();
}

try {
    $conn->begin_transaction();

    // 1. Create Order
    $user_id = $_SESSION['user_id'];
    $total = calculateOrderTotal($conn);

    // Check if 'created_at' exists in orders table
    $hasCreatedAt = false;
    $result = $conn->query("SHOW COLUMNS FROM orders LIKE 'created_at'");
    if ($result && $result->num_rows > 0) {
        $hasCreatedAt = true;
    }

    if ($hasCreatedAt) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'Pending', NOW())");
        $stmt->bind_param("id", $user_id, $total);
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("id", $user_id, $total);
    }
    $stmt->execute();
    $order_id = $conn->insert_id;

    // 2. Add Order Items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        // Get product price
        $product_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product = $product_stmt->get_result()->fetch_assoc();

        // Ensure size_id is set and valid
        $size_id = isset($item['size_id']) && $item['size_id'] ? $item['size_id'] : null;
        if (!$size_id) {
            // Try to get a default size_id from the database
            $size_stmt = $conn->prepare("SELECT size_id FROM product_sizes WHERE product_id = ? LIMIT 1");
            $size_stmt->bind_param("i", $product_id);
            $size_stmt->execute();
            $size_row = $size_stmt->get_result()->fetch_assoc();
            $size_id = $size_row ? $size_row['size_id'] : 1; // fallback to 1 if not found
        }

        // Insert order item
        $stmt = $conn->prepare("INSERT INTO order_items 
                            (order_id, product_id, size_id, quantity, price, subtotal)
                            VALUES (?, ?, ?, ?, ?, ?)");
        $subtotal = $product['price'] * $item['quantity'];
        $stmt->bind_param("iiiidd", $order_id, $product_id, $size_id, 
                        $item['quantity'], $product['price'], $subtotal);
        $stmt->execute();

        // Update stock
        $update_stmt = $conn->prepare("UPDATE product_sizes 
                                    SET stock = stock - ? 
                                    WHERE product_id = ? AND size_id = ?");
        $update_stmt->bind_param("iii", $item['quantity'], $product_id, $size_id);
        $update_stmt->execute();
    }

    // 3. Create Payment
    // Check if 'order_id' exists in payments table
    $hasOrderId = false;
    $result = $conn->query("SHOW COLUMNS FROM payments LIKE 'order_id'");
    if ($result && $result->num_rows > 0) {
        $hasOrderId = true;
    }

    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'online';
    if ($hasOrderId) {
        $stmt = $conn->prepare("INSERT INTO payments (order_id, method, status) VALUES (?, ?, 'completed')");
        $stmt->bind_param("is", $order_id, $payment_method);
    } else {
        // If you have a different column, change here
        $stmt = $conn->prepare("INSERT INTO payments (method, status) VALUES (?, 'completed')");
        $stmt->bind_param("s", $payment_method);
    }
    $stmt->execute();

    $conn->commit();

    // Clear cart
    unset($_SESSION['cart']);

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order placed successfully!'
    ]);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Order failed: ' . $e->getMessage()
    ]);
    exit();
}

// Helper function to calculate order total using actual product prices
function calculateOrderTotal($conn) {
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $subtotal += $item['quantity'] * $product['price'];
    }
    $shipping = 50;
    $tax = $subtotal * 0.12;
    return $subtotal + $shipping + $tax;
}
?>