<?php
session_start();
require_once 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars(trim($_POST['type']), ENT_QUOTES, 'UTF-8');
    $priority = htmlspecialchars(trim($_POST['priority']), ENT_QUOTES, 'UTF-8');
    $assigned_to = (int)$_POST['assigned_to'];
    $created_by = $_SESSION['user_id'];

    // Check for missing fields
    if (empty($title) || empty($description) || empty($type) || empty($priority) || empty($assigned_to)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: create_issue.php");
        exit();
    }

    // Insert issue into the database
    $stmt = $conn->prepare("
        INSERT INTO issues (title, description, type, priority, status, assigned_to, created_by, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'Open', ?, ?, NOW(), NOW())
    ");
    $stmt->bind_param('ssssii', $title, $description, $type, $priority, $assigned_to, $created_by);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Issue created successfully.";
        header("Location: dashboard.php");
        exit();
    } else {
        error_log("Error creating issue: " . $stmt->error);
        $_SESSION['error'] = "An error occurred while creating the issue.";
        header("Location: create_issue.php");
        exit();
    }
}
?>
