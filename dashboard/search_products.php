<?php
session_start();
include '../includes/auth.php';
include '../includes/db_connect.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $stmt = $conn->prepare("SELECT * FROM products 
                          WHERE name LIKE ? 
                          OR category LIKE ? 
                          OR description LIKE ? 
                          OR barcode LIKE ?
                          ORDER BY created_at DESC");
    $searchPattern = "%$searchTerm%";
    $stmt->bind_param("ssss", $searchPattern, $searchPattern, $searchPattern, $searchPattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'products' => $products]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}