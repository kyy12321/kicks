<?php
// $servername = "localhost";
// $username = "root";
// $password = ""; // Typically root has no password in XAMPP by default
// $dbname = "kicksf";

$servername = "localhost";
$username = "u866427573_kicks";
$password = "@Qetu1357"; // Typically root has no password in XAMPP by default
$dbname = "u866427573_kicks";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "";
?>