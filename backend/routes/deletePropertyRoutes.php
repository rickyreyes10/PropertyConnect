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
            case 'delete':
                try {
                    // Check if propertyId is provided
                    if (!isset($_POST['propertyId']) || empty($_POST['propertyId'])) {
                        throw new Exception("Property ID is required");
                    }

                    // Verify that the property belongs to the logged-in user
                    $userId = SessionManager::getUserID();
                    if (!$property->verifyPropertyOwner($userId, $_POST['propertyId'])) {
                        throw new Exception("Unauthorized access to property");
                    }

                    $result = $property->deleteProperty($_POST['propertyId']);
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? "Property deleted successfully" : "Failed to delete property"
                    ]);
                } catch (Exception $e) {
                    error_log("Error deleting property: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => "Error deleting property: " . $e->getMessage()
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