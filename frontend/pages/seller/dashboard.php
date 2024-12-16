<?php 
require_once '../../../backend/config/sessionManagement.php';
require_once '../../../backend/models/Property.php';
require_once '../../../backend/config/db.php';

SessionManager::init();

if (!SessionManager::isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$userId = SessionManager::getUserId();
$username = SessionManager::getUsername();

// Get properties
$database = new Database();
$conn = $database->connect();
$property = new Property($conn);
$properties = $property->getPropertiesByUserId($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="img-src 'self' data: https: http:;">
    <title>Seller Dashboard - PropertyConnect</title>
    <link rel="stylesheet" href="/~pkim35/WP/PW/Project4/PropertyConnect/frontend/assets/css/main.css" type="text/css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <?php include '../../components/sellerHeader.php'; ?>

    <main class="dashboard-container">
        <h1>My Properties</h1>
        
        <div class="property-grid">
            <!-- Add Property Card -->
            <div class="property-card add-property">
                <div class="add-property-button" id="addPropertyBtn">
                    <div class="add-icon">+</div>
                    <p>Add New Property</p>
                </div>
            </div>

            <!-- Property Cards -->
            <?php if ($properties): ?>
                <!-- Debug: Print image URLs -->
                <?php foreach ($properties as $property): 
                    error_log("Image URL for property " . $property['PropertyID'] . ": " . $property['ImageURL']);
                endforeach; ?>
                
                <?php foreach ($properties as $property): ?>
                    <div class="property-card" data-property-id="<?php echo $property['PropertyID']; ?>">
                        <div class="property-image">
                            <img src="../../../backend/routes/imageProxy.php?url=<?php echo urlencode($property['ImageURL']); ?>" 
                                 alt="Property Image" 
                                 onerror="this.src='../../assets/default-property.jpg';">
                        </div>
                        <div class="property-info">
                            <h3><?php echo htmlspecialchars($property['Location']); ?></h3>
                            <p class="price">$<?php echo number_format($property['PropertyTax']); ?></p>
                            <p class="tax">Property Tax (7%): $<?php echo number_format($property['PropertyTax'] * 0.07, 2); ?></p>
                            <p class="details">
                                <?php echo htmlspecialchars($property['Bedrooms']); ?> beds • 
                                <?php echo htmlspecialchars($property['Bathrooms']); ?> baths • 
                                <?php echo htmlspecialchars($property['FloorPlan']); ?> sqft
                            </p>
                            <div class="property-actions">
                                <button class="btn-update" data-property-id="<?php echo $property['PropertyID']; ?>">Update</button>
                                <button class="btn-delete" data-property-id="<?php echo $property['PropertyID']; ?>">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Add Property Modal -->
    <div id="addPropertyModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Property</h2>
            <form id="addPropertyForm">
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-group">
                    <label for="age">Property Age (years)</label>
                    <input type="number" id="age" name="age" required>
                </div>

                <div class="form-group">
                    <label for="floorPlan">Floor Plan (sq ft)</label>
                    <input type="text" id="floorPlan" name="floorPlan" required>
                </div>

                <div class="form-group">
                    <label for="bedrooms">Number of Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" required>
                </div>

                <div class="form-group">
                    <label for="bathrooms">Number of Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" required>
                </div>

                <div class="form-group">
                    <label for="garden">Garden</label>
                    <select id="garden" name="garden" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="parking">Parking Available</label>
                    <select id="parking" name="parking" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="proximityFacilities">Near Facilities (schools, towns)</label>
                    <select id="proximityFacilities" name="proximityFacilities" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="proximityRoads">Near Main Roads</label>
                    <select id="proximityRoads" name="proximityRoads" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="propertyTax">Property Value ($)</label>
                    <input type="number" id="propertyTax" name="propertyTax" required>
                    <p id="taxEstimate" class="tax-estimate"></p>
                </div>

                <div class="form-group">
                    <label for="imageURL">Image URL</label>
                    <input type="url" id="imageURL" name="imageURL" required 
                           placeholder="Enter a valid image URL (e.g., https://example.com/image.jpg)"
                           pattern="https?://.+"
                           title="Please enter a valid HTTP or HTTPS image URL">
                    <small class="form-text text-muted">
                        Tip: Right-click on an image online and select "Copy image address" to get a valid URL
                    </small>
                </div>

                <button type="submit" class="submit-btn">Add Property</button>
            </form>
        </div>
    </div>

    <!-- Update Property Modal -->
    <div id="updatePropertyModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Property</h2>
            <form id="updatePropertyForm">
                <input type="hidden" id="updatePropertyId" name="propertyId">
                
                <div class="form-group">
                    <label for="updateLocation">Location</label>
                    <input type="text" id="updateLocation" name="location" required>
                </div>

                <div class="form-group">
                    <label for="updateAge">Property Age (years)</label>
                    <input type="number" id="updateAge" name="age" required>
                </div>

                <div class="form-group">
                    <label for="updateFloorPlan">Floor Plan (sq ft)</label>
                    <input type="text" id="updateFloorPlan" name="floorPlan" required>
                </div>

                <div class="form-group">
                    <label for="updateBedrooms">Number of Bedrooms</label>
                    <input type="number" id="updateBedrooms" name="bedrooms" required>
                </div>

                <div class="form-group">
                    <label for="updateBathrooms">Number of Bathrooms</label>
                    <input type="number" id="updateBathrooms" name="bathrooms" required>
                </div>

                <div class="form-group">
                    <label for="updateGarden">Garden</label>
                    <select id="updateGarden" name="garden" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updateParking">Parking Available</label>
                    <select id="updateParking" name="parking" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updateProximityFacilities">Near Facilities (schools, towns)</label>
                    <select id="updateProximityFacilities" name="proximityFacilities" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updateProximityRoads">Near Main Roads</label>
                    <select id="updateProximityRoads" name="proximityRoads" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updatePropertyTax">Property Value ($)</label>
                    <input type="number" id="updatePropertyTax" name="PropertyTax" required>
                    <p id="updateTaxEstimate" class="tax-estimate"></p>
                </div>

                <div class="form-group">
                    <label for="updateImageURL">Image URL</label>
                    <input type="url" id="updateImageURL" name="imageURL" required>
                </div>

                <button type="submit" class="submit-btn">Update Property</button>
            </form>
        </div>
    </div>

    <footer>
    <div class="footer-content">
        <div class="footer-section">
            <h4>Contact Us</h4>
            <p>Email: info@propertyconnect.com</p>
            <p>Phone: (555) 123-4567</p>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <a href="../../pages/index.php">Home |</a>
            <a href="../../pages/login.php">Login |</a>
            <a href="../../pages/register.php">Register |</a>
            <a href="../../pages/payment-info.php">Payment Information</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 PropertyConnect. All rights reserved.</p>
    </div>
</footer>
    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>