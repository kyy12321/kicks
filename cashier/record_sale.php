<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

header('Content-Type: application/json');

// Only allow logged-in cashiers
if (!isLoggedIn() || $_SESSION['role'] !== 'cashier') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$items = $data['items'] ?? [];
$subtotal = $data['subtotal'] ?? 0;
$tax = $data['tax'] ?? 0;
$total = $data['total'] ?? 0;
$payment_method = $data['payment_method'] ?? '';
$cashier_username = $_SESSION['username'] ?? null;

// Get cashier_id from users table
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $cashier_username);
$stmt->execute();
$stmt->bind_result($cashier_id);
$stmt->fetch();
$stmt->close();

if (!$cashier_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cashier not found']);
    exit();
}

if (empty($items) || $total <= 0 || !$payment_method) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Incomplete sale data']);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert into sales table
    $stmt = $conn->prepare("INSERT INTO sales (subtotal, tax_amount, total_amount, cashier_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("dddi", $subtotal, $tax, $total, $cashier_id);
    $stmt->execute();
    $sale_id = $stmt->insert_id;
    $stmt->close();

    // Insert each item into sales_items and update stock
    $itemStmt = $conn->prepare("INSERT INTO sales_items (sale_id, product_id, size_id, quantity, price_per_unit, tax_rate) VALUES (?, ?, ?, ?, ?, ?)");
    $stockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

    foreach ($items as $item) {
        $pid = $item['id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        $size_id = isset($item['size_id']) ? $item['size_id'] : 1; // Default size_id if not used
        $tax_rate = 0.02;

        // Insert sale item
        $itemStmt->bind_param("iiiidd", $sale_id, $pid, $size_id, $qty, $price, $tax_rate);
        $itemStmt->execute();

        // Update stock (if you use product_sizes, update that table instead)
        $stockStmt->bind_param("iii", $qty, $pid, $qty);
        $stockStmt->execute();

        if ($stockStmt->affected_rows === 0) {
            throw new Exception("Insufficient stock for product ID $pid");
        }
    }
    $itemStmt->close();
    $stockStmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'sale_id' => $sale_id]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>