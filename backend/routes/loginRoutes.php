<?php

// Prevent PHP from outputting HTML errors
ini_set('display_errors', 0);

// Ensure we're sending JSON response
header('Content-Type: application/json');

require_once '../models/User.php';
require_once '../config/db.php';
require_once '../config/sessionManagement.php';
require_once '../config/constants.php';

try {
    // Create database connection
    $database = new Database();
    $conn = $database->connect();
    
    // Initialize User model
    $user = new User($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch($action) {
            case 'login':
                try {
                    $result = $user->login(
                        $_POST['identifier'], // This can be username or email
                        $_POST['password']
                    );
                    
                    if ($result) {
                        // Initialize session
                        SessionManager::init();
                        
                        // Set session data
                        SessionManager::setUserSession(
                            $result['UserID'],
                            $result['Username'],
                            $result['RoleID']
                        );
                        
                        echo json_encode([
                            'success' => true,
                            'message' => "Login successful"
                        ]);
                    } else {
                        throw new Exception("Invalid credentials");
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Login failed: " . $e->getMessage()
                    ]);
                }
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => "Invalid action"
                ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Invalid request method"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Server error"
    ]);
}
?>