<?php
require_once '../config/sessionManagement.php';


//this is the class for the authentication middleware.
class Auth {
    //method for checking if the user is logged in
    public static function requireLogin() { //this is to check if the user is logged in
        SessionManager::init(); //initialize session
        if (!SessionManager::isLoggedIn()) { //if the user is not logged in
            echo json_encode([
                'success' => false, //return false
                'message' => 'Authentication required' //return the error message
            ]);
            exit; //stop the execution of the script
        }
    }

    // method for checking if the user has to be a specific role for a specific route .. this can/would be useful if we implemented the buyer and admin dashboards aswell to redirect to.. 
    public static function requireRole($roleID) {
        self::requireLogin(); // Ensure user is logged in first
        if (SessionManager::getUserRole() !== $roleID) { //if the user's role is not the same as the roleID
            echo json_encode([
                'success' => false, //return false
                'message' => 'Unauthorized access' //return the error message
            ]);
            exit; //stop the execution of the script
        }
    }
}
?>