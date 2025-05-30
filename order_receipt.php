<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';

if (!isset($_GET['id']) || !isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get Order Details
$stmt = $conn->prepare("SELECT o.*, u.username, p.method AS payment_method 
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      LEFT JOIN payments p ON o.id = p.order_id
                      WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found");
}

// Get Order Items
$stmt = $conn->prepare("SELECT oi.*, p.name, p.image_path, s.size 
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.id
                      JOIN sizes s ON oi.size_id = s.id
                      WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt - KICKS</title>
    <style>
        .receipt { max-width: 800px; margin: 2rem auto; padding: 2rem; background: #fff; }
        .header { text-align: center; border-bottom: 2px solid #eee; margin-bottom: 2rem; }
        .logo { font-size: 24px; color: #4CAF50; margin-bottom: 1rem; }
        .order-info { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; margin: 2rem 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .total { font-size: 18px; font-weight: bold; text-align: right; }
        .print-btn { background: #4CAF50; color: white; padding: 12px 24px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="logo">KICKS</div>
            <h1>Order Receipt</h1>
            <p>Order #<?= $order['id'] ?></p>
        </div>

        <div class="order-info">
            <div>
                <h3>Customer Information</h3>
                <p><?= htmlspecialchars($order['username']) ?></p>
                <p>Order Date: <?= date('M j, Y H:i', strtotime($order['created_at'])) ?></p>
            </div>
            <div>
                <h3>Payment Information</h3>
                <p>Method: <?= strtoupper($order['payment_method']) ?></p>
                <p>Status: <?= ucfirst($order['status']) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                             style="width: 50px; vertical-align: middle;">
                        <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td><?= $item['size'] ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <p>Grand Total: ₱<?= number_format($order['total'], 2) ?></p>
        </div>

        <center>
            <button onclick="window.print()" class="print-btn">
                Print Receipt
            </button>
        </center>
    </div>
</body>
</html>
</qodoArtifact>
```

Update your cart.php button to:

```php