<?php

// Prevent PHP from outputting HTML errors
ini_set('display_errors', 0);

// Ensure we're sending JSON response
header('Content-Type: application/json');

require_once '../models/creditCard.php';
require_once '../config/db.php';
require_once '../config/sessionManagement.php';

try {
    // Create database connection
    $database = new Database();
    $conn = $database->connect();
    
    // Initialize session
    SessionManager::init();
    
    // Check if user is logged in
    if (!SessionManager::isLoggedIn()) {
        throw new Exception("User not logged in");
    }

    // Get userID from session
    $userID = SessionManager::getUserId();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? ''; 

        switch($action) {
            case 'savePayment':
                try {
                    $creditCard = new CreditCard($conn);
                    $cardResult = $creditCard->storeCreditCardInfo(
                        $userID,
                        $_POST['cardType'],
                        substr($_POST['cardNumber'], -4),
                        $_POST['expiryMonth'],
                        $_POST['expiryYear'],
                        $_POST['cvv'],
                        $_POST['billingAddress'],
                        $_POST['phone']
                    );

                    if ($cardResult) {
                        echo json_encode([
                            'success' => true,
                            'message' => "Payment information saved successfully"
                        ]);
                    } else {
                        throw new Exception("Failed to store credit card information");
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Credit card error: " . $e->getMessage()
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
        'message' => $e->getMessage()
    ]);
}
?>