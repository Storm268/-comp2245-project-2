<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Database connection settings
$servername = "localhost";
$db_username = "root";
$db_password = ""; // Replace with your MySQL password (if any)
$dbname = "bugme";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize query for fetching tickets
$filter = "ALL";
$where_clause = "";

// Handle the filtering logic based on GET parameter
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    if ($filter === "OPEN") {
        $where_clause = "WHERE status = 'OPEN'";
    } elseif ($filter === "MYTICKETS") {
        $user_id = $_SESSION['user_id'];
        $where_clause = "WHERE assigned_to = '$user_id'";
    }
}

// Query to fetch issues with relevant data
$sql = "SELECT issues.id, issues.title, issues.type, issues.status, users.firstname, users.lastname, issues.created_at 
        FROM issues 
        JOIN users ON issues.assigned_to = users.id 
        $where_clause";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | BugMe Issue Tracker</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Additional styling specific to dashboard */
        .filter-buttons {
            margin: 20px 0;
        }
        .filter-buttons button {
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .filter-buttons .active {
            background-color: #007bff;
            color: #fff;
        }
        .issue-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .issue-table th, .issue-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .status-open {
            color: green;
            font-weight: bold;
        }
        .status-closed {
            color: red;
            font-weight: bold;
        }
        .status-in-progress {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>BugMe Issue Tracker</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="signup.html">Add User</a></li>
                <li><a href="create_issue.php">New Issue</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="wrapper">
        <h1>Issues</h1>
        <div class="filter-buttons">
            <a href="dashboard.php?filter=ALL"><button class="<?php echo $filter == 'ALL' ? 'active' : ''; ?>">ALL</button></a>
            <a href="dashboard.php?filter=OPEN"><button class="<?php echo $filter == 'OPEN' ? 'active' : ''; ?>">OPEN</button></a>
            <a href="dashboard.php?filter=MYTICKETS"><button class="<?php echo $filter == 'MYTICKETS' ? 'active' : ''; ?>">MY TICKETS</button></a>
            <a href="create_issue.php" class="create-issue-btn">Create New Issue</a>
        </div>

        <table class="issue-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_class = strtolower(str_replace(' ', '-', $row['status']));
                        echo "<tr>
                                <td>#{$row['id']} {$row['title']}</td>
                                <td>{$row['type']}</td>
                                <td class='status-{$status_class}'>{$row['status']}</td>
                                <td>{$row['firstname']} {$row['lastname']}</td>
                                <td>" . date("Y-m-d", strtotime($row['created_at'])) . "</td>
                                <td><a href='view_issue.php?id={$row['id']}'>View Full Description</a></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No issues found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
