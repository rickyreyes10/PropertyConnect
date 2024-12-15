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
            case 'get':
                if (!isset($_POST['propertyId'])) {
                    throw new Exception("Property ID is required");
                }
                
                $propertyData = $property->getPropertyById($_POST['propertyId']);
                if ($propertyData) {
                    echo json_encode([
                        'success' => true,
                        'property' => $propertyData
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => "Property not found"
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
