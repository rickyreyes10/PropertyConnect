<?php 

require_once __DIR__ . '/../config/db.php'; //require the database connection

class Property {    
    private $conn; //this is the connection to the database

    public function __construct($conn) { //constructor to initialize the connection to the database
        $this->conn = $conn;
    }

    //this method is used to add a property to the database
    public function addProperty($userID, $location, $age, $floorPlan, $bedrooms, $bathrooms, $garden, $parking, $proximityToFacilities, $proximityToMainRoads, $propertyTax, $imageURL) {
        try {
            // Download and store image locally
            $imageData = file_get_contents($imageURL);
            $fileName = uniqid() . '.jpg';
            $localPath = '../uploads/' . $fileName;
            file_put_contents($localPath, $imageData);
            $imageURL = '/~pkim35/WP/PW/Project4/PropertyConnect/backend/uploads/' . $fileName;
            //prepare the SQL statement to insert a new property into the database
            $stmt = $this->conn->prepare("INSERT INTO Property (UserID, Location, Age, FloorPlan, Bedrooms, Bathrooms, Garden, Parking, ProximityFacilities, ProximityRoads, PropertyTax, ImageURL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) { //check if the statement failed
                throw new Exception("Prepare failed: " . $this->conn->error); //throw an exception if the statement failed
            }

            // Convert boolean values to '1' or '0' strings
            $gardenValue = $garden ? '1' : '0';
            $parkingValue = $parking ? '1' : '0';
            $proximityFacilitiesValue = $proximityToFacilities ? '1' : '0';
            $proximityRoadsValue = $proximityToMainRoads ? '1' : '0';

            //bind the parameters to the statement
            $stmt->bind_param("isisiissssds", $userID, $location, $age, $floorPlan, $bedrooms, $bathrooms, $gardenValue, $parkingValue, $proximityFacilitiesValue, $proximityRoadsValue, $propertyTax, $imageURL);
            
            //execute the statement
            $result = $stmt->execute();
            //close the statement
            $stmt->close();
            
            //return the result of the statement which is a boolean value (true or false)
            return $result;
            
        } catch (Exception $e) { //catch any exceptions
            error_log("Error adding property: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    //this method is used to get all properties by a user
    public function getPropertiesByUserId($userID) {
        try { //using try-catch to handle any exceptions that may occur
            $stmt = $this->conn->prepare("SELECT * FROM Property WHERE UserID = ?"); //prepare the SQL statement to select all properties from the database where the userID is equal to the userID passed to the function
            
            if (!$stmt) { //check if the statement failed
                throw new Exception("Prepare failed: " . $this->conn->error); //throw an exception if the statement failed
            }

            $stmt->bind_param("i", $userID); //bind the userID parameter to the statement
            $stmt->execute(); //execute the statement
            $result = $stmt->get_result(); //get the result of the statement which is a mysqli_result object containing the query results which could be zero rows (no properties for that user) or one row (properties for that user)
            $stmt->close(); //close the statement
            
            return $result; //return the result of the statement which is an mysqli_result object containing the query results which could be zero rows (no properties for that user) or one row (properties for that user)
        } catch (Exception $e) { //catch any exceptions
            error_log("Error getting properties: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    // Additional methods we might want to add based on Milestone 3 requirements:
    
    // Get single property details
    public function getPropertyById($propertyId) { //this method is used to get a single property by its ID
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Property WHERE PropertyID = ?"); //prepare the SQL statement to select all properties from the database where the propertyID is equal to the propertyID passed to the function
            $stmt->bind_param("i", $propertyId); //bind the propertyID parameter to the statement
            $stmt->execute(); //execute the statement
            $result = $stmt->get_result(); //get the result of the statement which is a mysqli_result object containing the query results which could be zero rows (no property with that ID) or one row (property with that ID)
            $stmt->close(); //close the statement
            
            return $result->fetch_assoc(); //return the result of the statement which is an associative array containing the property details for example it could look like this: ['PropertyID' => 1, 'UserID' => 1, 'Location' => '123 Main St', 'Age' => 10, 'FloorPlan' => '1000 sq ft', 'Bedrooms' => 3, 'Bathrooms' => 2, 'Garden' => 1, 'Parking' => 1, 'ProximityFacilities' => 1, 'ProximityRoads' => 1, 'Tax' => 1000, 'ImageURL' => 'https://example.com/image.jpg']
        } catch (Exception $e) { //catch any exceptions
            error_log("Error getting property: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    // Update property details
    //this method is used to update the details of a property
    public function updateProperty($propertyId, $location, $age, $floorPlan, $bedrooms, $bathrooms, $garden, $parking, $proximityFacilities, $proximityRoads, $propertyTax, $imageURL) {
        try {
            $stmt = $this->conn->prepare("UPDATE Property SET 
                Location = ?, 
                Age = ?, 
                FloorPlan = ?, 
                Bedrooms = ?, 
                Bathrooms = ?, 
                Garden = ?, 
                Parking = ?, 
                ProximityFacilities = ?, 
                ProximityRoads = ?, 
                PropertyTax = ?, 
                ImageURL = ? 
                WHERE PropertyID = ?");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            // Convert boolean values to '1' or '0' strings
            $gardenValue = $garden ? '1' : '0';
            $parkingValue = $parking ? '1' : '0';
            $proximityFacilitiesValue = $proximityFacilities ? '1' : '0';
            $proximityRoadsValue = $proximityRoads ? '1' : '0';

            $stmt->bind_param("sisiiiiisssi", 
                $location, 
                $age, 
                $floorPlan, 
                $bedrooms, 
                $bathrooms, 
                $gardenValue, 
                $parkingValue, 
                $proximityFacilitiesValue, 
                $proximityRoadsValue, 
                $propertyTax, 
                $imageURL,
                $propertyId
            );
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error updating property: " . $e->getMessage());
            throw $e;
        }
    }

    // Delete property
    public function deleteProperty($propertyId) { //this method is used to delete a property from the database
        try {
            $stmt = $this->conn->prepare("DELETE FROM Property WHERE PropertyID = ?"); //prepare the SQL statement to delete a property from the database where the propertyID is equal to the propertyID passed to the function
            $stmt->bind_param("i", $propertyId); //bind the propertyID parameter to the statement
            $result = $stmt->execute(); //execute the statement
            $stmt->close(); //close the statement
            
            return $result; //return the result of the statement which is a boolean value (true or false)   
        } catch (Exception $e) { //catch any exceptions
            error_log("Error deleting property: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }

    public function verifyPropertyOwner($userId, $propertyId) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Property WHERE PropertyID = ? AND UserID = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $propertyId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_row()[0];
            
            $stmt->close();
            
            // Returns true if the property belongs to the user (count = 1), false otherwise
            return $count > 0;
        } catch (Exception $e) {
            error_log("Property ownership verification error: " . $e->getMessage());
            throw $e;
        }
    }

    public function calculatePropertyTax($value) {
        return $value * 0.07;  // 7% of property value
    }
}   

?>