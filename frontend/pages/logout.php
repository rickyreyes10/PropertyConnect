<?php
require_once '../../backend/config/sessionManagement.php';

// Initialize session
SessionManager::init();

// Destroy session
SessionManager::logout();

// Redirect to home page
header('Location: /~rreyespena1/wp/pw/p4/frontend/pages/index.php');
exit;
?>