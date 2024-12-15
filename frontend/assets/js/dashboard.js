document.addEventListener('DOMContentLoaded', function () {
    // Modal elements
    const addModal = document.getElementById('addPropertyModal');
    const updateModal = document.getElementById('updatePropertyModal');
    const addPropertyBtn = document.getElementById('addPropertyBtn');
    const closeButtons = document.querySelectorAll('.close');

    // Add Property Modal handling
    addPropertyBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        addModal.style.display = "block";
    });

    // Update Property Modal handling
    document.querySelectorAll('.btn-update').forEach(button => {
        button.addEventListener('click', function () {
            event.preventDefault();
            event.stopPropagation();
            const propertyId = this.dataset.propertyId;
            populateUpdateForm(propertyId);
            updateModal.style.display = "block";
        });
    });

    // Close modal functionality
    closeButtons.forEach(button => {
        button.onclick = function (event) {
            event.preventDefault();
            event.stopPropagation();
            addModal.style.display = "none";
            updateModal.style.display = "none";
        }
    });

    // Close modal when clicking outside
    window.onclick = function (event) {
        if (event.target == addModal || event.target == updateModal) {
            event.preventDefault();
            event.stopPropagation();
            addModal.style.display = "none";
            updateModal.style.display = "none";
        }
    }

    // Add Property functionality
    const addPropertyForm = document.getElementById('addPropertyForm');
    if (addPropertyForm) {
        addPropertyForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(addPropertyForm);
            formData.append('action', 'add');

            fetch('../../../backend/routes/addPropertyRoutes.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Property added successfully!');
                        window.location.reload(); // Refresh to show new property
                    } else {
                        alert(data.message || 'Failed to add property');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add property: ' + error.message);
                });
        });
    }

    // Update Property functionality
    const updatePropertyForm = document.getElementById('updatePropertyForm');
    if (updatePropertyForm) {
        updatePropertyForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(updatePropertyForm);
            formData.append('action', 'update');

            // Debug: Log what we're sending
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch('../../../backend/routes/updatePropertyRoutes.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text().then(text => {
                        console.log('Raw server response:', text); // Debug: Log raw response
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Error parsing JSON:', text);
                            throw new Error('Invalid JSON response from server: ' + text);
                        }
                    });
                })
                .then(data => {
                    console.log('Parsed response:', data); // Debug: Log parsed data
                    if (data.success) {
                        alert('Property updated successfully!');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to update property');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // More detailed error message
                    alert('Failed to update property. Error: ' + error.message + 
                          '\nCheck the browser console for more details.');
                });
        });
    }

    // Delete Property functionality
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const propertyId = this.dataset.propertyId;

            if (confirm('Are you sure you want to delete this property?')) {
                deleteProperty(propertyId);
            }
        });
    });

    // For making the entire card clickable (except the add property card)
    document.querySelectorAll('.property-card').forEach(card => {
        card.addEventListener('click', function (e) {
            // Skip if this is the add property card
            if (this.classList.contains('add-property')) {
                return;
            }
            
            // Don't navigate if clicking on buttons
            if (!e.target.closest('.property-actions')) {
                const propertyId = this.dataset.propertyId;
                window.location.href = `property-details.php?id=${propertyId}`;
            }
        });
    });

    function deleteProperty(propertyId) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('propertyId', propertyId);

        fetch('/~rreyespena1/wp/pw/p4/backend/routes/deletePropertyRoutes.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const propertyCard = document.querySelector(`.property-card[data-property-id="${propertyId}"]`);
                    if (propertyCard) {
                        propertyCard.remove();
                    }
                    alert('Property deleted successfully!');
                } else {
                    alert(data.message || 'Failed to delete property');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete property: ' + error.message);
            });
    }

    // Helper function to populate update form
    function populateUpdateForm(propertyId) {
        const formData = new FormData();
        formData.append('action', 'get');
        formData.append('propertyId', propertyId);

        fetch('../../../backend/routes/propertyRoutes.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('updatePropertyId').value = propertyId;
                    document.getElementById('updateLocation').value = data.property.Location;
                    document.getElementById('updateAge').value = data.property.Age;
                    document.getElementById('updateFloorPlan').value = data.property.FloorPlan;
                    document.getElementById('updateBedrooms').value = data.property.Bedrooms;
                    document.getElementById('updateBathrooms').value = data.property.Bathrooms;
                    document.getElementById('updateGarden').value = data.property.Garden;
                    document.getElementById('updateParking').value = data.property.Parking;
                    document.getElementById('updateProximityFacilities').value = data.property.ProximityFacilities;
                    document.getElementById('updateProximityRoads').value = data.property.ProximityRoads;
                    document.getElementById('updatePropertyTax').value = data.property.PropertyTax;
                    document.getElementById('updateImageURL').value = data.property.ImageURL;
                } else {
                    alert('Failed to load property details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load property details: ' + error.message);
            });
    }

    function updateTaxDisplay(inputId, displayId) {
        const valueInput = document.getElementById(inputId);
        const taxDisplay = document.getElementById(displayId);

        if (valueInput && taxDisplay) {
            valueInput.addEventListener('input', function () {
                const value = parseFloat(this.value) || 0;
                const tax = value * 0.07;
                taxDisplay.textContent = `Estimated Property Tax (7%): $${tax.toFixed(2)}`;
            });
        }
    }

    // For Add Property Form
    updateTaxDisplay('propertyTax', 'taxEstimate');

    // For Update Property Form
    updateTaxDisplay('updatePropertyTax', 'updateTaxEstimate');
});