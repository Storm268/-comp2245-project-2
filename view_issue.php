<?php
session_start();
require_once 'config.php'; // Include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get the issue ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Issue ID is missing.";
    exit();
}

$issue_id = (int)$_GET['id'];

// Fetch the issue details from the database
$stmt = $conn->prepare("
    SELECT issues.id, issues.title, issues.description, issues.type, issues.priority, issues.status, 
           issues.created_at, issues.updated_at, 
           users.firstname AS assigned_firstname, users.lastname AS assigned_lastname,
           created_by.firstname AS creator_firstname, created_by.lastname AS creator_lastname
    FROM issues
    JOIN users ON issues.assigned_to = users.id
    JOIN users AS created_by ON issues.created_by = created_by.id
    WHERE issues.id = ?
");
$stmt->bind_param("i", $issue_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Issue not found.";
    exit();
}

$issue = $result->fetch_assoc();

// Pass the data to PHP variables
$title = htmlspecialchars($issue['title']);
$description = htmlspecialchars($issue['description']); // Added this line
$assigned_to = htmlspecialchars($issue['assigned_firstname'] . " " . $issue['assigned_lastname']);
$type = htmlspecialchars($issue['type']);
$priority = htmlspecialchars($issue['priority']);
$status = htmlspecialchars($issue['status']);
$created_on = date("Y-m-d g:i A", strtotime($issue['created_at']));
$updated_on = date("Y-m-d g:i A", strtotime($issue['updated_at']));
$creator = htmlspecialchars($issue['creator_firstname'] . " " . $issue['creator_lastname']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Issue</title>
    <link rel="stylesheet" href="view_issue.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="sidebar">
        <h1>BugMe Issue Tracker</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="signup.php">Add User</a></li>
                <li><a href="create_issue.php">New Issue</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="issue-container">
            <div class="issue-header">
                <h1><?php echo $title; ?></h1>
                <h4>Issue #<?php echo $issue_id; ?></h4>
            </div>

            <div class="issue-description">
                <p><?php echo $description; ?></p>
            </div>

            <div class="issue-meta">
                <p><strong>Issue created on:</strong> <?php echo $created_on; ?> by <?php echo $creator; ?></p>
                <p><strong>Last updated on:</strong> <?php echo $updated_on; ?></p>
            </div>

            <div class="issue-details-container">
                <div class="details">
                    <p><strong>Assigned To:</strong> <?php echo $assigned_to; ?></p>
                    <p><strong>Type:</strong> <?php echo $type; ?></p>
                    <p><strong>Priority:</strong> <?php echo $priority; ?></p>
                    <p><strong>Status:</strong> <?php echo $status; ?></p>
                </div>

                <div class="actions">
                    <form method="POST" action="update-issue.php">
                        <input type="hidden" name="issue_id" value="<?php echo $issue_id; ?>">
                        <input type="hidden" name="status" value="Closed">
                        <button type="submit" class="btn btn-primary">Mark as Closed</button>
                    </form>

                    <form method="POST" action="update-issue.php">
                        <input type="hidden" name="issue_id" value="<?php echo $issue_id; ?>">
                        <input type="hidden" name="status" value="In Progress">
                        <button type="submit" class="btn btn-secondary">Mark In Progress</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
