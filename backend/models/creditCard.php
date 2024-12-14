<?php

require_once '../config/db.php';



class CreditCard{
    private $conn;

    // Constructor to initialize database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    //method to detect the type of credit card based on the card number
    public function detectCardType($cardNumber) {

        $cardNumber = preg_replace('/[\s-]/', '', $cardNumber); //remove any spaces or dashes from the card number
        
        //^4 must start with 4
        //[0-9]{12} followed by 12 digits
        //(?:[0-9]{3})? optionally followed by 3 digits
        //(?:...) non-capturing group which is a group of characters (in this case 3 digits) that are not included in the capture group
        //? that comes after the non-capturing group makes the group optional meaning the card number can have 12 or 15 digits (12 digits + 3 option digits)
        //$ at the end of the pattern indicates the end of the string
        //result is that the card must start with the number 4 followed by 12 digits (13 digits total) or 16 digits total b/c of the optional 3 digits. 
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            return 'Visa';
        }

        //^5 must start with 5
        //[1-5] followed by 1 digit between 1 to 5
        //[0-9]{14} followed by exactly 14 digits
        //$ end of the string
        //result is that the card must start with 5 followed by a digit between 1 to 5 and then 14 digits (16 digits total)
        else if (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber)) {
            return 'Mastercard';
        }

        //^3 must start with the number 3
        //[47] followed by either 4 or 7
        //[0-9]{13} followed by exactly 13 digits
        //result is that the card must start with 3 followed by either 4 or 7 and then 13 digits (15 digits total)
        else if (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
            return 'American Express';
        } 
        //return unknown if the card number does not match any of the patterns above
        else {
            return 'Unknown';
        }
    }

    //method to validate the card number
    //by first removing any spaces or dashes from the card number
    //then checking if the card number contains only digits
    //then implementing the Luhn algorithm to validate the card number in terms of proper formatting 
    //such as mistyped digits, switching two digits, extra digits, missing digits etc 
    public function validateCardNumber($cardNumber) {
        // Remove any spaces or dashes
        //preg_replace is used to replace all occurrences of the pattern (in this case spaces or dashes) with an empty string
        // \s is a shorthand character class that matches any whitespace character (space, tab, newline, etc.)
        // - is a literal dash character that matches a literl dash '-'
        //[] is a character class that matches any character in the set
        // '' this second argument (empty string) is the what to replace the pattern with
        // so we'll match any whitespace or dash and replace them with an empty string
        $cardNumber = preg_replace('/[\s-]/', '', $cardNumber);
        
        // Check if the card number contains only digits (0-9)
        //ctype_digit is a function that checks if all characters in the string are digits
        //meaning cardnumber should only contain digits
        if (!ctype_digit($cardNumber)) {
            return false;
        }

        // Implement Luhn algorithm aka mod 10 algorithm or modulus algorithm
        $sum = 0; //will hold the running total of the card number
        $length = strlen($cardNumber); //get the length of the card number 
        $parity = $length % 2; //parity determines which positions to double... ensures that regardless of the length of the card number, we maintain the same pattern of doubling every other digit

        //loop through the card number from right to left to process each digit (from left to right)
        for ($i = $length - 1; $i >= 0; $i--) {
            //get the current digit and convert it to an integer using intval()
            $digit = intval($cardNumber[$i]); 

            //if the current position is even (0-indexed), double the digit
            //if the current position is odd (1-indexed), do not double the digit
            //this ensures that we double every other digit starting from the rightmost digit 
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) { //if the digit is greater than 9, subtract 9 from it so that the digit after doubling is a single digit 
                    //this is done to handle the case where doubling a digit results in a two-digit number (10 or more).. 
                    //this is done to make the digits easier to validate, makes the final sum smaller and easier to check if it's divisible by 10
                    $digit -= 9;
                }
            }
            //add the digit to the sum
            $sum += $digit;
        }
        //if the sum modulo 10 is 0, the card number is valid
        //this is done to check if the card number is divisible by 10, which is a requirement for a valid card number
        //by design, credit card companies specifically choose the last digit (check digit) so that the luhn algorithm sum will be divisible by 10
        return ($sum % 10) == 0;
    }



    //method to validate the expiry date of the card
    public function validateExpirtyDate($month, $year) {
        $currentYear = (int)date('Y'); //get the current year and convert it to an integer
        $currentMonth = (int)date('m'); //get the current month and convert it to an integer

        //convert 2-digit year to 4-digit year
        if ($year < 100) { //if the year is less than 100, it is a 2-digit year for example 24 is 2024
            $year += 2000; //add 2000 to the year to make it a 4-digit year so 24 + 2000 = 2024
        }

        //check if the card has expired 
        // year < currentYear means the card has expired in the past 
        // year == currentYear && $month < $currentMonth means that the card expired (on this current year) on a previous month less than the current month
        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
            return false;
        }

        //check if month is valid (1-12) 
        if ($month < 1 || $month > 12) { //month must be between 1 and 12
            return false;
        }

        return true; //if the card is not expired, return true
    }


    //method to validate the CVV of the card
    public function validateCVV($cvv, $cardType) {
        //trim the cvv to remove any leading or trailing whitespace
        $cvv = trim($cvv);

        //check if the cvv contains non-digit characters
        if (!ctype_digit($cvv)) { 
            return false; //if the cvv is not only digits, return false meaning the cvv has non-digit characters
        }

        
        $length = strlen($cvv); //get the length of the cvv
        if ($cardType === 'American Express') { //American Express has a 4-digit CVV
            return $length === 4; //if the length of the cvv is 4, return true
        }

        return $length === 3; //for other card types, the cvv should be 3 digits... return true if the length of the cvv is 3
    }


    //method to store the credit card information in the database table CreditCard
    public function storeCreditCardInfo($userID, $cardType, $lastFourDigits, $expiryMonth, $expiryYear, $cvv, $billingAddress, $phone) {
        try {
            error_log("Starting credit card storage with connection: " . ($this->conn ? 'valid' : 'invalid'));
        
            if (!$this->conn) {
                throw new Exception("Database connection is not valid");
            }
            
            $stmt = $this->conn->prepare("INSERT INTO CreditCard 
                (UserID, CardType, LastFourDigits, CVV, ExpiryMonth, ExpiryYear, BillingAddress, PhoneNumber) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                error_log("SQL Prepare Error: " . $this->conn->error);
                throw new Exception("SQL Prepare Error: " . $this->conn->error);
            }

            error_log("SQL prepared successfully");
            error_log("Binding parameters...");

            $bindResult = $stmt->bind_param("isssiiss", 
                $userID, 
                $cardType, 
                $lastFourDigits, 
                $cvv,
                $expiryMonth, 
                $expiryYear,
                $billingAddress,
                $phone
            );

            if (!$bindResult) {
                error_log("Bind failed: " . $stmt->error);
                throw new Exception("Parameter binding failed");
            }

            error_log("Parameters bound successfully");
            error_log("Executing statement...");

            $result = $stmt->execute();

            if (!$result) {
                error_log("Execute Error: " . $stmt->error);
                throw new Exception("Execute Error: " . $stmt->error);
            }

            error_log("Credit card stored successfully");
            $stmt->close();
            return true;

        } catch (Exception $e) {
            error_log("Credit card storage error: " . $e->getMessage());
            throw $e;
        }
    }


    
}


?>