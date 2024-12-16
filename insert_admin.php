<?php
require_once 'config.php'; // Include your database connection file

try {
    // Prepare the SQL statement
    $stmt = $conn->prepare("
        INSERT INTO users (firstname, lastname, email, password, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    // Bind the parameters
    $firstname = "Admin";
    $lastname = "User";
    $email = "admin@project.com";
    $password = password_hash("admin", PASSWORD_BCRYPT); // Hash the password 'admin'

    $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);

    // Execute the query
    if ($stmt->execute()) {
        echo "Admin user successfully created!";
    } else {
        echo "Error: " . $stmt->error;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
