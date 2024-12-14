<?php 
require_once __DIR__ . '/../../backend/config/sessionManagement.php';
SessionManager::init();

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
            <a href="../index.php">Home</a>
            <a href="../payment-info.php">Payment Information</a>
            <?php if ($isLoggedIn): ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a href="../logout.php" class="logout-btn">Logout</a>
            <?php else: ?>
                <a href="../login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>
</header>