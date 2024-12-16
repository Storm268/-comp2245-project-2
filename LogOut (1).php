<?php
session_start();

// Clear all session variables
$_SESSION = array();


// Cancel the session
session_destroy();

// Return a response
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>
