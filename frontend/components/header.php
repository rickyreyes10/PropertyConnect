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
            <a href="/frontend/pages/index.php">PropertyConnect</a>
        </div>
        <div class="nav-links">
            <a href="/~rreyespena1/wp/pw/p4/frontend/pages/index.php">Home</a>
            <?php if ($isLoggedIn): ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a href="/~rreyespena1/wp/pw/p4/frontend/pages/logout.php" class="logout-btn">Logout?</a>
                <a href="/~rreyespena1/wp/pw/p4/frontend/pages/seller/dashboard.php">Seller Dashboard</a>
            <?php else: ?>
                <a href="/~rreyespena1/wp/pw/p4/frontend/pages/login.php">Login</a>
                <a href="/~rreyespena1/wp/pw/p4/frontend/pages/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>