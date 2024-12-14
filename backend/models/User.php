<?php
require_once '../config/db.php';

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    //this method is used to register a new user in the database
    public function register($firstName, $lastName, $email, $username, $password, $roleID) {
        try {
            // Check if email or username already exists
            if ($this->checkUserExists($email, $username)) { //check if the email or username already exists in the database
                throw new Exception("Email or username already exists"); //throw an exception if the email or username already exists
            }

            //hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            //prepare the SQL statement to insert a new user into the database
            $stmt = $this->conn->prepare("INSERT INTO User (FirstName, LastName, Email, Username, HashedPassword, RoleID) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) { //check if the statement failed to prepare
                throw new Exception("Prepare failed: " . $this->conn->error); //throw an exception if the statement failed to prepare
            }

            $stmt->bind_param("sssssi", $firstName, $lastName, $email, $username, $hashedPassword, $roleID); //bind the parameters to the statement
            $result = $stmt->execute(); //execute the statement
            $stmt->close(); //close the statement
            
            return $result; //return the result of the statement which is a boolean value (true or false)
        } catch (Exception $e) { //catch any exceptions
            error_log("Registration error: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    //this method is used to login a user in the database
    public function login($identifier, $password) {
        try {
            //prepare the SQL statement to select the userID, hashedPassword and roleID from the database where the username or email is equal to the identifier passed to the function
            $stmt = $this->conn->prepare("SELECT UserID, Username, HashedPassword, RoleID FROM User WHERE Username = ? OR Email = ?");
            if (!$stmt) { //check if the statement failed to prepare
                throw new Exception("Prepare failed: " . $this->conn->error); //throw an exception if the statement failed to prepare
            }

            $stmt->bind_param("ss", $identifier, $identifier); //bind the identifier parameter (username or email) to the statement
            $stmt->execute(); //execute the statement
            $result = $stmt->get_result(); //get the result of the statement
            
            //row = $result->fetch_assoc() makes an associative array of the result where it could look like this: ['UserID' => 1, 'HashedPassword' => 'hashedPassword', 'RoleID' => 1]
            if ($row = $result->fetch_assoc()) { //check if the result contains a row i.e. not null
                if (password_verify($password, $row['HashedPassword'])) { //check if the password is correct
                    $stmt->close(); //close the statement
                    return $row; //return the row which is an associative array with the userID, hashedPassword and roleID
                }
            }

            $stmt->close(); //close the statement
            return false; //return false if the username or password is incorrect
        } catch (Exception $e) { //catch any exceptions
            throw $e; //throw the exception
        }
    }

    //helper method to check if user exists
    //this method is used to check if a user exists in the database
    private function checkUserExists($email, $username) { 
        try {
            //prepare the SQL statement to select the userID from the database where the email is equal to the email passed to the function or the username is equal to the username passed to the function 
            $stmt = $this->conn->prepare("SELECT UserID FROM User WHERE Email = ? OR Username = ?");

            //bind the email and username parameters to the statement
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute(); //execute the statement
            $result = $stmt->get_result(); //get the result of the statement
            $exists = $result->num_rows > 0; //check if the result contains a row i.e. not null (true or false)
            $stmt->close(); //close the statement
            return $exists; //return the result of the statement which is a boolean value (true or false)
        } catch (Exception $e) { //catch any exceptions
            error_log("Error checking user existence: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    // method to get user details
    // this method is used to get the details of a user from the database
    public function getUserDetails($userID) {
        try {
            //prepare the SQL statement to select the userID, firstName, lastName, email, username and roleID from the database where the userID is equal to the userID passed to the function
            $stmt = $this->conn->prepare("SELECT UserID, FirstName, LastName, Email, Username, RoleID FROM User WHERE UserID = ?");
            //bind the userID parameter to the statement
            $stmt->bind_param("i", $userID);
            $stmt->execute(); //execute the statement
            $result = $stmt->get_result(); //get the result of the statement
            $user = $result->fetch_assoc(); //fetch the result as an associative array
            $stmt->close(); //close the statement
            return $user; //return the result of the statement which is an associative array with the userID, firstName, lastName, email, username and roleID
        } catch (Exception $e) {
            error_log("Error getting user details: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    //method to update user details
    //this method is used to update the details of a user in the database
    public function updateUser($userID, $firstName, $lastName, $email) {
        try {
            //prepare the SQL statement to update the firstName, lastName and email in the database where the userID is equal to the userID passed to the function
            $stmt = $this->conn->prepare("UPDATE User SET FirstName = ?, LastName = ?, Email = ? WHERE UserID = ?");
            //bind the firstName, lastName and email parameters to the statement
            $stmt->bind_param("sssi", $firstName, $lastName, $email, $userID);
            $result = $stmt->execute(); //execute the statement
            $stmt->close(); //close the statement
            return $result; //return the result of the statement which is a boolean value (true or false)
        } catch (Exception $e) { //catch any exceptions
            error_log("Error updating user: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }
}
?>
