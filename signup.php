<?php
// Database connection settings
$servername = "localhost";
$db_username = "root";
$db_password = ""; // Replace with your MySQL password (if any)
$dbname = "bugme"; // Your database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Check for empty fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Check for duplicate email
    $check_email_query = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($check_email_query);
    if ($result->num_rows > 0) {
        echo "Email is already registered. Please use a different email.";
        exit;
    }

    // Hash the password using PASSWORD_DEFAULT
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the users table
    $sql = "INSERT INTO users (firstname, lastname, email, password, created_at) 
            VALUES ('$first_name', '$last_name', '$email', '$hashed_password', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful! You can now <a href='login.html'>log in</a>.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
