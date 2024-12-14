<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PropertyConnect</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <main class="registration-container">
        <h2>Seller Registration</h2>
        
        <!-- User Details Form -->
        <form id="registrationForm" class="registration-form">
            <div class="form-section">
                <h3>Personal Information</h3>
                
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" required>
                    <span class="error" id="firstNameError"></span>
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" required>
                    <span class="error" id="lastNameError"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error" id="usernameError"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error" id="passwordError"></span>
                </div>
                <button type="submit" class="submit-btn">Register</button>
            </div>
            
        </form>
    </main>

    <?php include '../components/footer.php'; ?>
    <script src="../assets/js/auth.js"></script>
</body>
</html>

