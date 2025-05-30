<?php
session_start();

header('Content-Type: application/json');

try {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'message' => 'Cart cleared']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error clearing cart']);
}