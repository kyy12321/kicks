<?php
include '../includes/db_connect.php'; // Ensure this path is correct

// Define the username, password, and role
$username = 'admin_demo';
$password = 'securepassword'; // Change this to a secure password
$role = 'admin';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashedPassword, $role);

// Execute the statement
if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>