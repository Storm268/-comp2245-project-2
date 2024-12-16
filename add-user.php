<?php
session_start();
header('Content-Type: application/json');

// Include database configuration
require_once 'config.php';

// Check if user is logged on and is currently an admin
if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@project2.com') {
    
    //if not, stop script
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

//Class to handle user
class UserHandler {
    private $pdo;

    // initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //Get users from database
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("
                SELECT id, firstname, lastname, email, created_at,
                CASE 
                    WHEN created_at > NOW() - INTERVAL 1 DAY THEN 'recent'
                    ELSE 'normal'
                END as status
                FROM Users 
                ORDER BY created_at DESC
            ");
            
            return ['success' => true, 'users' => $stmt->fetchAll()];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['error' => 'Error retrieving users'];
        }
    }

    //Function to add a new user to the database
    public function addUser($userData) {
        try {
            // Validate required fields
            $required = ['firstname', 'lastname', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($userData[$field])) {
                    return ['error' => ucfirst($field) . ' is required'];
                }
            }
    
            // Normalize and validate email
            $userData['email'] = strtolower(trim($userData['email']));
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                return ['error' => 'Invalid email format'];
            }
    
            // Validate name lengths
            if (strlen($userData['firstname']) > 50 || strlen($userData['lastname']) > 50) {
                return ['error' => 'First Name and Last Name must not exceed 50 characters'];
            }
    
            // Validate password
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $userData['password'])) {
                return ['error' => 'Password must have at least 8 characters, including one number, one letter, and one capital letter'];
            }
    
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM Users WHERE email = ?");
            $stmt->execute([$userData['email']]);
            if ($stmt->rowCount() > 0) {
                return ['error' => 'Email already exists'];
            }
    
            // Sanitize and hash password
            $hashedPassword = password_hash(trim($userData['password']), PASSWORD_DEFAULT);
    
            // Insert new user into database
            $stmt = $this->pdo->prepare("
                INSERT INTO Users (firstname, lastname, email, password, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                htmlspecialchars($userData['firstname'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($userData['lastname'], ENT_QUOTES, 'UTF-8'),
                $userData['email'],
                $hashedPassword
            ]);
    
            return ['success' => true, 'message' => 'User added successfully'];
    
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['error' => 'Error adding user'];
        }
    }
}