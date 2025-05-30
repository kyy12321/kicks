<?php
session_start();
include '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['role'];

if ($role === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($role === 'cashier') {
    header("Location: cashier_dashboard.php");
    exit();
} else {
    echo "Access Denied.";
    exit();
}
?>