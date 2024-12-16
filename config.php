<?php
// Database connection settings
$servername = "localhost"; // Database server (usually localhost for XAMPP)
$username = "root";        // Default XAMPP username
$password = "";            // Default password (leave empty for XAMPP)
$dbname = "bugme";         // Replace this with your database name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
