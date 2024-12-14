<?php 

// Prevent PHP from outputting HTML errors
ini_set('display_errors', 0);

// Ensure we're sending JSON response
header('Content-Type: application/json');

require_once '../models/User.php';
require_once '../config/db.php';
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
            case 'register':
                try {
                    $result = $user->register(
                        $_POST['firstName'],
                        $_POST['lastName'],
                        $_POST['email'],
                        $_POST['username'],
                        $_POST['password'],
                        ROLE_SELLER
                    );

                    if ($result) {
                        echo json_encode([
                            'success' => true,
                            'message' => "Registration successful"
                        ]);
                    } else {
                        throw new Exception("User registration failed");
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Registration failed: " . $e->getMessage()
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