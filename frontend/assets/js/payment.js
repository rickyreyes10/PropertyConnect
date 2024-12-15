document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('paymentForm');
    const cardNumberInput = document.getElementById('cardNumber');
    const cardTypeDisplay = document.getElementById('cardType');
    let detectedCardType = ''; // store the detected card type

    // Update card type when user types card number
    cardNumberInput.addEventListener('input', function () {
        detectedCardType = detectCardType(cardNumberInput.value);
        cardTypeDisplay.textContent = detectedCardType ? `Card Type: ${detectedCardType}` : '';
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent form submission for validation

        let isValid = true;

        // Validate credit card fields
        isValid &= validateField('cardNumber', /^\d{13,19}$/, 'Card number must be between 13 and 19 digits.');
        isValid &= validateField('cvv', /^\d{3,4}$/, 'CVV must be 3 or 4 digits.');
        isValid &= validateField('billingAddress', /.+/, 'Billing address is required.');
        isValid &= validateField('phone', /^\d{10,15}$/, 'Phone number must be between 10 and 15 digits.');

        if (isValid) {
            // Create form data object
            const formData = new FormData(form);
            formData.append('cardType', detectedCardType);
            formData.append('action', 'savePayment');


            // Send to backend
            fetch('/~rreyespena1/wp/pw/p4/backend/routes/creditCardRoutes.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    return response.text().then(text => {
                        try {
                            const data = JSON.parse(text);
                            return data;
                        } catch (e) {
                            console.error('Raw response:', text);
                            console.error('Parse error:', e);
                            throw new Error('Server returned invalid JSON. Check console for details.');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        alert('Payment information saved successfully!');
                        window.location.href ='../pages/seller/dashboard.php';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to save payment information: ' + error.message);
                });
        }
    });

    function validateField(id, regex, errorMessage) {
        const input = document.getElementById(id);
        const error = document.getElementById(`${id}Error`);
        if (!regex.test(input.value.trim())) {
            error.textContent = errorMessage;
            return false;
        } else {
            error.textContent = '';
            return true;
        }
    }

    function detectCardType(number) {
        const cleaned = number.replace(/\D/g, ''); // Remove non-digit characters
        if (/^4/.test(cleaned)) return 'Visa';
        if (/^5[1-5]/.test(cleaned)) return 'Mastercard';
        if (/^3[47]/.test(cleaned)) return 'American Express';
        return null;
    }
}); 