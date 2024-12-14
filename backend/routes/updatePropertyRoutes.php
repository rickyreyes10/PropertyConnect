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
            case 'update':
                try {
                    // Check if propertyId is provided
                    if (!isset($_POST['propertyId']) || empty($_POST['propertyId'])) {
                        throw new Exception("Property ID is required");
                    }

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

                    // Verify that the property belongs to the logged-in user
                    $userId = SessionManager::getUserID();
                    if (!$property->verifyPropertyOwner($userId, $_POST['propertyId'])) {
                        throw new Exception("Unauthorized access to property");
                    }

                    $result = $property->updateProperty(
                        $_POST['propertyId'],
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
                        'message' => $result ? "Property updated successfully" : "Failed to update property"
                    ]);
                } catch (Exception $e) {
                    error_log("Error updating property: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => "Error updating property: " . $e->getMessage()
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