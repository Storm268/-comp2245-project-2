<?php
session_start();
require_once 'config.php'; // Include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issue_id = isset($_POST['issue_id']) ? (int)$_POST['issue_id'] : null;
    $status = isset($_POST['status']) ? htmlspecialchars(trim($_POST['status']), ENT_QUOTES, 'UTF-8') : null;

    // Validate inputs
    if (empty($issue_id) || empty($status)) {
        echo json_encode(["error" => "ID and status are required"]);
        exit();
    }

    // Update the issue status
    $stmt = $conn->prepare("UPDATE issues SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $status, $issue_id);

    if ($stmt->execute()) {
        // Redirect to the dashboard on success
        header("Location: dashboard.php");
        exit();
    } else {
        echo json_encode(["error" => "Error updating issue: " . $stmt->error]);
        exit();
    }
}
?>
