<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure no whitespace or other output before headers
if (ob_get_level()) ob_end_clean();
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
        error_log("Action received: " . $action);

        switch($action) {
            case 'update':
                try {
                    // Debug output
                    error_log("Raw POST data: " . file_get_contents('php://input'));
                    error_log("Received POST data: " . print_r($_POST, true));

                    // Basic input validation
                    $requiredFields = ['propertyId', 'location', 'age', 'floorPlan', 'bedrooms', 
                                     'bathrooms', 'garden', 'parking', 
                                     'proximityFacilities', 'proximityRoads', 
                                     'PropertyTax', 'imageURL'];
                    
                    foreach ($requiredFields as $field) {
                        if (!isset($_POST[$field])) {
                            error_log("Missing field: " . $field);
                            throw new Exception("Missing required field: $field");
                        }
                    }

                    // Convert string boolean values to actual booleans
                    $garden = filter_var($_POST['garden'], FILTER_VALIDATE_BOOLEAN);
                    $parking = filter_var($_POST['parking'], FILTER_VALIDATE_BOOLEAN);
                    $proximityFacilities = filter_var($_POST['proximityFacilities'], FILTER_VALIDATE_BOOLEAN);
                    $proximityRoads = filter_var($_POST['proximityRoads'], FILTER_VALIDATE_BOOLEAN);

                    // Verify property belongs to current user
                    if (!$property->verifyPropertyOwner(SessionManager::getUserID(), $_POST['propertyId'])) {
                        throw new Exception("Unauthorized access to property");
                    }

                    $result = $property->updateProperty(
                        $_POST['propertyId'],
                        $_POST['location'],
                        intval($_POST['age']),
                        $_POST['floorPlan'],
                        intval($_POST['bedrooms']),
                        intval($_POST['bathrooms']),
                        $garden,
                        $parking,
                        $proximityFacilities,
                        $proximityRoads,
                        floatval($_POST['PropertyTax']),
                        $_POST['imageURL']
                    );
                    
                    $response = [
                        'success' => $result,
                        'message' => $result ? "Property updated successfully" : "Failed to update property"
                    ];
                    error_log("Sending response: " . print_r($response, true));
                    echo json_encode($response);
                    exit;

                } catch (Exception $e) {
                    error_log("Error updating property: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => "Error updating property: " . $e->getMessage()
                    ]);
                    exit;
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