<?php


//session management class
//every time a user logs in, a session is created and a session ID is generated
//the session ID is stored in a cookie on the user's browser
//the session ID is used to identify the session when the user makes a request to the server
//the session data is stored on the server  
//there can be other session data associated with the session such as userID, username, roleID, etc.
class SessionManager {
    // Initialize session if not already started
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session lifetime to 5 hours
            ini_set('session.gc_maxlifetime', 18000);
            session_set_cookie_params(18000);
            session_start();
        }
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['userID']);
    }

    // Get current user's ID
    public static function getUserID() {
        return $_SESSION['userID'] ?? null;
    }

    // Get current user's role
    public static function getUserRole() {
        return $_SESSION['roleID'] ?? null;
    }

    // Get current username
    public static function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    // Set session data after successful login
    public static function setUserSession($userID, $username, $roleID) {
        $_SESSION['userID'] = $userID;
        $_SESSION['username'] = $username;
        $_SESSION['roleID'] = $roleID;
    }

    // Clear session data (logout)
    public static function logout() {
        session_unset();
        session_destroy();
    }

    // Regenerate session ID (for security)
    public static function regenerateSession() {
        session_regenerate_id(true);
    }
}
?>