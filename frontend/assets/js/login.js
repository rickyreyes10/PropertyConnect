document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            let isValid = true;

            // Validate identifier (username or email)
            isValid &= validateField('identifier', /.+/, 'Username or Email is required.');

            // Validate password
            isValid &= validateField('password', /.+/, 'Password is required.');

            if (isValid) {
                const formData = new FormData(loginForm);
                formData.append('action', 'login');

                fetch('/~rreyespena1/wp/pw/p4/backend/routes/loginRoutes.php', {
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
                        if (data.success) { // Check if login was successful
                            // Always redirect to seller dashboard since we're only handling sellers
                            window.location.href = '/~rreyespena1/wp/pw/p4/frontend/pages/seller/dashboard.php';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Login failed: ' + error.message);
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