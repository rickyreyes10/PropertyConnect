<?php 
require_once '../../../backend/config/sessionManagement.php';
require_once '../../../backend/models/Property.php';
require_once '../../../backend/config/db.php';

SessionManager::init();

if (!SessionManager::isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Get property ID from URL
$propertyId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$propertyId) {
    header('Location: dashboard.php');
    exit;
}

// Get property details
$database = new Database();
$conn = $database->connect();
$propertyModel = new Property($conn);
$property = $propertyModel->getPropertyById($propertyId);

// Verify property exists and belongs to current user
if (!$property || $property['UserID'] != SessionManager::getUserId()) {
    header('Location: dashboard.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - PropertyConnect</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/property-details.css">
</head>
<body>
    <?php include '../../components/sellerHeader.php'; ?>

    <main class="property-details-container">
        <div class="back-button">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
        </div>

        <div class="property-details">
            <div class="property-image">
                <img src="../../../backend/routes/imageProxy.php?url=<?php echo urlencode($property['ImageURL']); ?>" 
                     alt="Property Image" 
                     onerror="this.src='../../assets/default-property.jpg';" 
                     style="width: 100%; height: auto; max-height: 400px; object-fit: cover;">
            </div>

            <div class="property-info">
                <h1><?php echo htmlspecialchars($property['Location']); ?></h1>
                
                <div class="price-info">
                    <h2>Property Value: $<?php echo number_format($property['PropertyTax']); ?></h2>
                    <p class="tax">Property Tax (7%): $<?php echo number_format($property['PropertyTax'] * 0.07, 2); ?></p>
                </div>

                <div class="property-specs">
                    <div class="spec-item">
                        <strong>Age:</strong> <?php echo htmlspecialchars($property['Age']); ?> years
                    </div>
                    <div class="spec-item">
                        <strong>Floor Plan:</strong> <?php echo htmlspecialchars($property['FloorPlan']); ?> sq ft
                    </div>
                    <div class="spec-item">
                        <strong>Bedrooms:</strong> <?php echo htmlspecialchars($property['Bedrooms']); ?>
                    </div>
                    <div class="spec-item">
                        <strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['Bathrooms']); ?>
                    </div>
                    <div class="spec-item">
                        <strong>Garden:</strong> <?php echo $property['Garden'] ? 'Yes' : 'No'; ?>
                    </div>
                    <div class="spec-item">
                        <strong>Parking:</strong> <?php echo $property['Parking'] ? 'Yes' : 'No'; ?>
                    </div>
                    <div class="spec-item">
                        <strong>Near Facilities:</strong> <?php echo $property['ProximityFacilities'] ? 'Yes' : 'No'; ?>
                    </div>
                    <div class="spec-item">
                        <strong>Near Main Roads:</strong> <?php echo $property['ProximityRoads'] ? 'Yes' : 'No'; ?>
                    </div>
                </div>

                <div class="property-actions">
                    <button class="btn-update" id="updatePropertyBtn">Update Property</button>
                    <button class="btn-delete" onclick="deleteProperty(<?php echo $propertyId; ?>)">Delete Property</button>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../components/footer.php'; ?>

    <!-- Update Property Modal -->
    <div id="updatePropertyModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Property</h2>
            <form id="updatePropertyForm">
                <input type="hidden" id="updatePropertyId" name="propertyId" value="<?php echo $propertyId; ?>">
                
                <div class="form-group">
                    <label for="updateLocation">Location</label>
                    <input type="text" id="updateLocation" name="location" value="<?php echo htmlspecialchars($property['Location']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="updateAge">Property Age (years)</label>
                    <input type="number" id="updateAge" name="age" value="<?php echo htmlspecialchars($property['Age']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="updateFloorPlan">Floor Plan (sq ft)</label>
                    <input type="text" id="updateFloorPlan" name="floorPlan" value="<?php echo htmlspecialchars($property['FloorPlan']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="updateBedrooms">Number of Bedrooms</label>
                    <input type="number" id="updateBedrooms" name="bedrooms" value="<?php echo htmlspecialchars($property['Bedrooms']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="updateBathrooms">Number of Bathrooms</label>
                    <input type="number" id="updateBathrooms" name="bathrooms" value="<?php echo htmlspecialchars($property['Bathrooms']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="updateGarden">Garden</label>
                    <select id="updateGarden" name="garden" required>
                        <option value="1" <?php echo $property['Garden'] ? 'selected' : ''; ?>>Yes</option>
                        <option value="0" <?php echo !$property['Garden'] ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updateParking">Parking Available</label>
                    <select id="updateParking" name="parking" required>
                        <option value="1" <?php echo $property['Parking'] ? 'selected' : ''; ?>>Yes</option>
                        <option value="0" <?php echo !$property['Parking'] ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updateProximityFacilities">Near Facilities (schools, towns)</label>
                    <select id="updateProximityFacilities" name="proximityFacilities" required>
                        <option value="1" <?php echo $property['ProximityFacilities'] ? 'selected' : ''; ?>>Yes</option>
                        <option value="0" <?php echo !$property['ProximityFacilities'] ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updateProximityRoads">Near Main Roads</label>
                    <select id="updateProximityRoads" name="proximityRoads" required>
                        <option value="1" <?php echo $property['ProximityRoads'] ? 'selected' : ''; ?>>Yes</option>
                        <option value="0" <?php echo !$property['ProximityRoads'] ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updatePropertyTax">Property Value ($)</label>
                    <input type="number" id="updatePropertyTax" name="PropertyTax" value="<?php echo htmlspecialchars($property['PropertyTax']); ?>" required>
                    <p id="updateTaxEstimate" class="tax-estimate"></p>
                </div>

                <div class="form-group">
                    <label for="imageURL">Image URL</label>
                    <input type="url" id="imageURL" name="imageURL" required 
                           value="<?php echo htmlspecialchars($property['ImageURL']); ?>"
                           placeholder="Enter a valid image URL (e.g., https://example.com/image.jpg)"
                           pattern="https?://.+"
                           title="Please enter a valid HTTP or HTTPS image URL">
                    <small class="form-text text-muted">
                        Please provide a direct link to an image (URL should end with .jpg, .png, .gif, etc.)
                    </small>
                </div>

                <button type="submit" class="submit-btn">Update Property</button>
            </form>
        </div>
    </div>

    <script>
        function deleteProperty(propertyId) {
            if (confirm('Are you sure you want to delete this property?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('propertyId', propertyId);

                fetch('../../../backend/routes/deletePropertyRoutes.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Property deleted successfully!');
                        window.location.href = 'dashboard.php';
                    } else {
                        alert(data.message || 'Failed to delete property');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete property: ' + error.message);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const updateModal = document.getElementById('updatePropertyModal');
            const updateBtn = document.getElementById('updatePropertyBtn');
            const closeBtn = updateModal.querySelector('.close');

            updateBtn.onclick = function() {
                updateModal.style.display = "block";
            }

            closeBtn.onclick = function() {
                updateModal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == updateModal) {
                    updateModal.style.display = "none";
                }
            }

            const updatePropertyForm = document.getElementById('updatePropertyForm');
            updatePropertyForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const formData = new FormData(updatePropertyForm);
                formData.append('action', 'update');

                fetch('../../../backend/routes/updatePropertyRoutes.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Property updated successfully!');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to update property');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update property: ' + error.message);
                });
            });
        });
    </script>
</body>
</html> 