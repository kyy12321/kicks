<?php
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'superadmin') {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)$_POST['product_id'];
    $sizeId = (int)$_POST['size_id'];
    $stock = (int)$_POST['stock'];

    // Check if entry exists
    $check = $conn->prepare("SELECT * FROM product_sizes WHERE product_id = ? AND size_id = ?");
    $check->bind_param('ii', $productId, $sizeId);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        // Update existing
        $stmt = $conn->prepare("UPDATE product_sizes SET stock = ? WHERE product_id = ? AND size_id = ?");
        $stmt->bind_param('iii', $stock, $productId, $sizeId);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $productId, $sizeId, $stock);
    }

    if ($stmt->execute()) {
        exit(json_encode(['success' => true]));
    } else {
        exit(json_encode(['success' => false, 'message' => 'Database error']));
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $productId = (int)$_GET['product_id'];
    $sizeId = (int)$_GET['size_id'];
    
    $stmt = $conn->prepare("DELETE FROM product_sizes WHERE product_id = ? AND size_id = ?");
    $stmt->bind_param('ii', $productId, $sizeId);
    
    if ($stmt->execute()) {
        exit(json_encode(['success' => true]));
    } else {
        exit(json_encode(['success' => false, 'message' => 'Delete failed']));
    }
}