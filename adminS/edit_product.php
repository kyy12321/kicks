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

// Validate required fields
$required = ['product_id', 'product_name', 'product_category', 'product_price', 'product_barcode'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        header('HTTP/1.1 400 Bad Request');
        exit(json_encode(['status' => 'error', 'message' => "Missing required field: $field"]));
    }
}

try {
    $productId = (int)$_POST['product_id'];
    $productName = $_POST['product_name'];
    $productCategory = $_POST['product_category'];
    $productPrice = (float)$_POST['product_price'];
    $productStock = isset($_POST['product_stock']) ? (int)$_POST['product_stock'] : 0;
    $productDescription = $_POST['product_description'] ?? '';
    $productBarcode = $_POST['product_barcode'];
    $discountType = $_POST['discount_type'] ?? 'none';
    $discountValue = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;

    // Handle file upload
    $imagePath = null;
    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($_FILES['product_image']['name']);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile);
        $imagePath = $targetFile;
    }

    // Update query
    if ($imagePath) {
        $stmt = $conn->prepare("UPDATE products SET 
            name = ?, category = ?, price = ?, stock = ?, description = ?, 
            image_path = ?, barcode = ?, discount_type = ?, discount_value = ?
            WHERE id = ?");
        $stmt->bind_param("ssdissssdi", 
            $productName, $productCategory, $productPrice, $productStock, $productDescription,
            $imagePath, $productBarcode, $discountType, $discountValue, $productId);
    } else {
        $stmt = $conn->prepare("UPDATE products SET 
            name = ?, category = ?, price = ?, stock = ?, description = ?, 
            barcode = ?, discount_type = ?, discount_value = ?
            WHERE id = ?");
        // Corrected parameter types: s s d i s s s d i
        $stmt->bind_param("ssdissddi", 
            $productName, $productCategory, $productPrice, $productStock, $productDescription,
            $productBarcode, $discountType, $discountValue, $productId);
    }

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => [
                'id' => $productId,
                'name' => $productName,
                'category' => $productCategory,
                'price' => $productPrice,
                'stock' => $productStock,
                'description' => $productDescription,
                'barcode' => $productBarcode,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'image_path' => $imagePath ?: $_POST['current_image']
            ]
        ]);
    } else {
        throw new Exception('Database update failed: ' . $conn->error);
    }

} catch (Exception $e) {
    error_log("Update error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'status' => 'error', 
        'message' => 'Product update failed: ' . $e->getMessage()
    ]);
}
