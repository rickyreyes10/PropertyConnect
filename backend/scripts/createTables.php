<?php
// Database connection details
$host = "localhost";
$user = "rreyespena1";
$password = "rreyespena1";
$db = "rreyespena1";

// Establish the database connection
$conn = mysqli_connect($host, $user, $password, $db);

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL statement to create tables
$sql = "
CREATE TABLE Role (
    RoleID INT AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(50) NOT NULL
);

CREATE TABLE User (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Username VARCHAR(50) UNIQUE NOT NULL,
    HashedPassword VARCHAR(255) NOT NULL,
    RoleID INT NOT NULL,
    FOREIGN KEY (RoleID) REFERENCES Role(RoleID)
);

CREATE TABLE Property (
    PropertyID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Location VARCHAR(255),
    Age INT,
    FloorPlan VARCHAR(255),
    Bedrooms INT,
    Bathrooms INT,
    Garden BOOLEAN,
    Parking BOOLEAN,
    ProximityToFacilities TEXT,
    ProximityToMainRoads TEXT,
    PropertyTax DECIMAL(10,2),
    ImageURL VARCHAR(255),
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);

CREATE TABLE CreditCard (
    CardID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT NOT NULL,
    CardType VARCHAR(20) NOT NULL,
    LastFourDigits CHAR(4) NOT NULL,
    CVV CHAR(4) NOT NULL,
    ExpiryMonth INT NOT NULL,
    ExpiryYear INT NOT NULL,
    BillingAddress TEXT,          
    PhoneNumber VARCHAR(15),      
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);
";

// Execute the SQL statement
if (mysqli_multi_query($conn, $sql)) {
    echo "Tables created successfully!";
} else {
    echo "Error creating tables: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);

//also 
/*
-- We need to insert the default seller role first
INSERT INTO Role (RoleID, RoleName) VALUES (1, 'Seller');
 */
?>
