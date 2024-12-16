<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

try {
    $filter = $_GET['filter'] ?? 'all';

    // Build the query based on the filter
    $query = "
        SELECT Issues.id, Issues.title, Issues.type, Issues.priority, Issues.status, 
               CONCAT(Users.firstname, ' ', Users.lastname) AS assigned_to, 
               Issues.created_at
        FROM Issues
        JOIN Users ON Issues.assigned_to = Users.id
    ";

    if ($filter === 'open') {
        $query .= " WHERE Issues.status = 'Open'";
    } elseif ($filter === 'my_tickets') {
        $query .= " WHERE Issues.assigned_to = :user_id";
    }

    $query .= " ORDER BY Issues.created_at DESC";
    $stmt = $pdo->prepare($query);

    // Bind the parameter if filtering by "my_tickets"
    if ($filter === 'my_tickets') {
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
    }

    $stmt->execute();
    echo json_encode(['success' => true, 'issues' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'Error fetching issues']);
}
?>
