<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['order_complete']) || !isset($_GET['id'])) {
    header("Location: cart.php");
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get order details
$order_stmt = $conn->prepare("
    SELECT o.*, u.username, u.email, 
           p.method AS payment_method, p.status AS payment_status 
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.id = ? AND o.user_id = ?
");
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order not found";
    header("Location: orders.php");
    exit();
}

// Get order items
$items_stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image_path 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Clear the order completion flag
unset($_SESSION['order_complete']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - KICKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .receipt-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .logo {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 1rem;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .order-details table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .order-details th, 
        .order-details td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .order-details th {
            background-color: #f8f9fa;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .total-section {
            text-align: right;
            margin-top: 2rem;
            font-size: 1.1rem;
        }

        .print-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 200px;
            margin: 2rem auto;
            transition: background 0.3s;
        }

        .print-btn:hover {
            background: #45a049;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-shoe-prints"></i> KICKS
            </div>
            <h1>Order Confirmation</h1>
            <p>Thank you for your purchase!</p>
        </div>

        <div class="order-info">
            <div>
                <h3>Order Details</h3>
                <p>Order Number: #<?= $order['id'] ?></p>
                <p>Date: <?= date('M j, Y h:i A', strtotime($order['created_at'])) ?></p>
                <p>Status: <?= ucfirst($order['status']) ?></p>
            </div>

            <div>
                <h3>Customer Information</h3>
                <p><?= htmlspecialchars($_SESSION['checkout_info']['first_name'] ?? '') ?> 
                   <?= htmlspecialchars($_SESSION['checkout_info']['last_name'] ?? '') ?></p>
                <p><?= htmlspecialchars($_SESSION['checkout_info']['email'] ?? '') ?></p>
                <p><?= htmlspecialchars($_SESSION['checkout_info']['phone'] ?? '') ?></p>
            </div>
        </div>

        <div class="order-details">
            <h3>Items Purchased</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <img src="uploads/<?= htmlspecialchars($item['image_path']) ?>" 
                                 class="product-image"
                                 alt="<?= htmlspecialchars($item['name']) ?>">
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <p>Subtotal: ₱<?= number_format($order['total'] - 50 - ($order['total'] * 0.12), 2) ?></p>
            <p>Shipping: ₱50.00</p>
            <p>Tax (12%): ₱<?= number_format($order['total'] * 0.12, 2) ?></p>
            <h3>Grand Total: ₱<?= number_format($order['total'], 2) ?></h3>
        </div>

        <div class="payment-info">
            <h3>Payment Information</h3>
            <p>Method: <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
            <p>Status: <?= ucfirst($order['payment_status']) ?></p>
        </div>

        <button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <?php unset($_SESSION['checkout_info']); ?>
</body>
</html></qodoArtifact>
```

Key updates needed in process_checkout.php:

```php