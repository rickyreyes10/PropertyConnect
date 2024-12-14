<?php 

require_once '../../backend/config/sessionManagement.php';
SessionManager::init();

//if already logged in, redirect to dashboard
if (SessionManager::isLoggedIn()) {
    header('Location: /~rreyespena1/wp/pw/p4/frontend/pages/seller/dashboard.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PropertyConnect</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <main class="registration-container">
        <h2>Login</h2>
        
        <form id="loginForm" class="registration-form">
            <div class="form-section">
                <div class="form-group">
                    <label for="identifier">Username or Email</label>
                    <input type="text" id="identifier" name="identifier" required>
                    <span class="error" id="identifierError"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error" id="passwordError"></span>
                </div>

                <button type="submit" class="submit-btn">Login</button>
            </div>

            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </form>
    </main>

    <?php include '../components/footer.php'; ?>
    <script src="../assets/js/login.js"></script>
</body>
</html>