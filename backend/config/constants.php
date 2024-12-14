<?php

//this file is used to define constants for the backend such as roles, property related constants, image upload settings, and boolean values for the database
//the reason for this file is to make the code more readable and maintainable

// Role definitions
define('ROLE_SELLER', 1);    // For seller accounts
define('ROLE_BUYER', 2);     // For future buyer accounts
define('ROLE_ADMIN', 3);     // For future admin accounts

// Property-related constants
define('MIN_BEDROOMS', 1); // Minimum number of bedrooms
define('MAX_BEDROOMS', 10); // Maximum number of bedrooms
define('MIN_BATHROOMS', 1); // Minimum number of bathrooms
define('MAX_BATHROOMS', 10); // Maximum number of bathrooms
define('MIN_AGE', 0); // Minimum property age
define('MAX_AGE', 200); // Maximum property age

// Image upload settings
define('UPLOAD_PATH', '../uploads/');  // Path for property images
define('MAX_FILE_SIZE', 5242880);      // 5MB in bytes 
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Tax-related constants
define('MIN_TAX', 0); // Minimum tax amount 
define('MAX_TAX', 1000000);  // Maximum reasonable tax amount

// Boolean values for database
define('DB_TRUE', 1); // True value for database
define('DB_FALSE', 0); // False value for database

?>