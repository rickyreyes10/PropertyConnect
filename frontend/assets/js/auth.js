document.addEventListener('DOMContentLoaded', function () {
    // Handle registration form
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function (event) {
            event.preventDefault();

            let isValid = true;

            // Validate user fields only
            isValid &= validateField('firstName', /^[a-zA-Z\s]+$/, 'First name is required and must be alphabetic.');
            isValid &= validateField('lastName', /^[a-zA-Z\s]+$/, 'Last name is required and must be alphabetic.');
            isValid &= validateField('email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'Valid email is required.');
            isValid &= validateField('username', /^[a-zA-Z0-9_]{3,20}$/, 'Username must be between 3 and 20 characters and can only contain letters, numbers, and underscores.');
            isValid &= validateField('password', /.{6,}/, 'Password must be at least 6 characters long.');

            if (isValid) {
                const formData = new FormData(registrationForm);
                formData.append('action', 'register');

                fetch('/~rreyespena1/wp/pw/p4/backend/routes/userRoutes.php', {
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
                            alert('Registration successful!');
                            window.location.href = '/~rreyespena1/wp/pw/p4/frontend/pages/login.php';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Registration failed: ' + error.message);
                    });
            }
        });
    }

    // Helper function for field validation
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
});