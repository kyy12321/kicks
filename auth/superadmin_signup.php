<?php
include '../includes/db_connect.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $barcode = $_POST['barcode'];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if username or barcode already exists
    $checkQuery = "SELECT * FROM users WHERE username = ? OR barcode = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username or barcode already exists. Please choose a different username or barcode.";
    } else {
        // Insert new superadmin user
        $insertQuery = "INSERT INTO users (username, password, role, barcode) VALUES (?, ?, 'superadmin', ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $username, $hashedPassword, $barcode);

        if ($stmt->execute()) {
            echo "Superadmin account created successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Sign-Up</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Superadmin Sign-Up</h1>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="barcode">Barcode:</label>
        <input type="text" id="barcode" name="barcode" required>
        <br>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>