<?php 

header('Content-Type: application/json');

require_once '../models/Property.php';
require_once '../config/db.php';
require_once '../config/sessionManagement.php';
require_once '../middleware/auth.php';

try {
    $database = new Database();
    $conn = $database->connect();
    
    $property = new Property($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        Auth::requireLogin();

        $action = $_POST['action'] ?? '';

        switch($action) {
            case 'add':  // Changed from 'addProperty' to 'add' to match frontend
                try {
                    // Basic input validation
                    $requiredFields = ['location', 'age', 'floorPlan', 'bedrooms', 
                                     'bathrooms', 'garden', 'parking', 
                                     'proximityFacilities', 'proximityRoads', 
                                     'tax', 'imageURL'];
                    
                    foreach ($requiredFields as $field) {
                        if (!isset($_POST[$field]) || empty($_POST[$field])) {
                            throw new Exception("Missing required field: $field");
                        }
                    }

                    $result = $property->addProperty(
                        SessionManager::getUserID(),
                        $_POST['location'],
                        $_POST['age'],
                        $_POST['floorPlan'],
                        $_POST['bedrooms'],
                        $_POST['bathrooms'],
                        $_POST['garden'],
                        $_POST['parking'],
                        $_POST['proximityFacilities'],
                        $_POST['proximityRoads'],
                        $_POST['tax'],
                        $_POST['imageURL']
                    );
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? "Property added successfully" : "Failed to add property"
                    ]);
                } catch (Exception $e) {
                    error_log("Error adding property: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => "Error adding property: " . $e->getMessage()
                    ]);
                }
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => "Invalid action"
                ]);
                break;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Invalid request method"
        ]);
    }

} catch (Exception $e) {
    error_log("Server error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Server error"
    ]);
}
?>