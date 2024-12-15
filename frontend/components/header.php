<?php 

require_once __DIR__ . '/../../backend/config/sessionManagement.php';
SessionManager::init();

//determine if user is logged in
$isLoggedIn = SessionManager::isLoggedIn();
$username = SessionManager::getUsername();

?>

<header>
    <link rel="stylesheet" href="../assets/css/main.css">
    <nav class="navbar">
        <div class="logo">
            <img src="../assets/logo.png" href="PropertyConnect">
        </div>
        <div class="nav-links">
            <a href="../pages/index.php">Home</a>
            <?php if ($isLoggedIn): ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a href="../pages/logout.php" class="logout-btn">Logout</a>
                <a href="../pages/seller/dashboard.php">Seller Dashboard</a>
            <?php else: ?>
                <a href="../pages/login.php">Login</a>
                <a href="../pages/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>