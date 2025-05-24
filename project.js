document.addEventListener('DOMContentLoaded', function () {
    const receptionistForm = document.getElementById('receptionistForm');
    const passwordInput = document.getElementById('pass');
    const togglePasswordIcon = document.querySelector('.toggle-password');
    const showEye = document.querySelector('.show-eye');
    const hideEye = document.querySelector('.hide-eye');
    const emailConfirmationCheckbox = document.getElementById('request');
    const emailRequiredSpan = document.getElementById('emailRequired');
    let isPasswordVisible = false;

    // Hide "Required*" for email initially
    emailRequiredSpan.style.display = 'none';

    // Password toggle
    togglePasswordIcon.addEventListener('click', function () {
        isPasswordVisible = !isPasswordVisible;
        passwordInput.type = isPasswordVisible ? 'text' : 'password';
        passwordInput.style.backgroundColor = isPasswordVisible ? 'white' : 'lightgreen';
        passwordInput.parentNode.classList.toggle('visible', isPasswordVisible);
    });

    // Email "Required*" toggle
    emailConfirmationCheckbox.addEventListener('change', function () {
        emailRequiredSpan.style.display = this.checked ? 'inline' : 'none';
    });

    // Initial background color for password input
    passwordInput.style.backgroundColor = 'lightgreen';

    // Form submission handler
    receptionistForm.addEventListener('submit', function (event) {
        event.preventDefault();
        validate();
    });

    // VALIDATE FUNCTION
    function validate() {
        const fields = [
            { id: 'fname', name: "Representative's First Name" },
            { id: 'Lname', name: "Representative's Last Name" },
            {
                id: 'pass',
                name: "Representative's Password",
                validator: (value) => {
                    const startsWithSpecial = /^[!@#$%^&*]/.test(value);
                    const containsUpper = /[A-Z]/.test(value);
                    const containsDigit = /\d/.test(value);
                    const isCorrectLength = value.length <= 6;

                    console.log("Password:", value);
                    console.log("Starts with special:", startsWithSpecial);
                    console.log("Contains uppercase:", containsUpper);
                    console.log("Contains digit:", containsDigit);
                    console.log("Correct length:", isCorrectLength);

                    return startsWithSpecial && containsUpper && containsDigit && isCorrectLength;
                },
                error: "Password must start with a special character (!@#$%^&*), contain at least 1 uppercase letter, 1 number, and be 6 characters max."
            },
            {
                id: 'id',
                name: "Representative's ID",
                validator: (value) => /^\d{3}$/.test(value),
                error: "Representative's ID must be a 3-digit number."
            },
            {
                id: 'phone',
                name: "Representative's Phone #",
                validator: (value) => /^\d{3}[-.\s]?\d{3}[-.\s]?\d{4}$/.test(value),
                error: "Phone number must be a valid 10-digit number."
            },
            {
                id: 'email',
                name: "Representative's Email",
                validator: (value) => !emailConfirmationCheckbox.checked || /^[^\s@]+@[^\s@]+\.[^\s@]{2,5}$/.test(value),
                error: "Please enter a valid email address."
            }
        ];

        for (const field of fields) {
            const input = document.getElementById(field.id);
            const value = input.value.trim();

            if (field.id !== 'email' && value === "") {
                alert(`${field.name} is required. Please enter.`);
                input.focus();
                return;
            }

            if (field.validator && !field.validator(value)) {
                alert(field.error || `${field.name} is invalid or missing required formatting.`);
                input.focus();
                return;
            }
        }

        verify();
    }

    // VERIFY FUNCTION
    function verify() {
        const firstName = document.getElementById('fname').value;
        const lastName = document.getElementById('Lname').value;
        const password = document.getElementById('pass').value;
        const id = document.getElementById('id').value;
        const phone = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        const transaction = document.getElementById('transaction').value;
        const emailConfirmation = emailConfirmationCheckbox.checked;

        const formData = new FormData();
        formData.append('fname', firstName);
        formData.append('Lname', lastName);
        formData.append('pass', password);
        formData.append('id', id);
        formData.append('phone', phone);
        formData.append('email', email);
        formData.append('request', emailConfirmation ? 'on' : 'off');
        formData.append('transaction', transaction);

        fetch('verify_user.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log("Response from PHP:", data);

                if (data.success) {
                    sessionStorage.setItem('repId', data.repId);
                    sessionStorage.setItem('repName', data.repName);

                    switch (transaction) {
                        case 'account':
                            window.location.href = 'search_account.html';
                            break;
                        case 'book':
                            window.location.href = 'book_rental.html';
                            break;
                        case 'cancel':
                            window.location.href = 'cancel_rental.html';
                            break;
                        case 'additional':
                            window.location.href = 'request_upgrades.html';
                            break;
                        case 'update':
                            window.location.href = 'update_upgrades.html';
                            break;
                        case 'new':
                            window.location.href = 'create_customer.html';
                            break;
                        default:
                            alert(`Welcome, ${firstName} ${lastName}!`);
                    }
                } else {
                    alert(data.error || `Receptionist ${firstName} ${lastName} not found or invalid credentials.`);
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                alert('Network error during verification. Check console for more info.');
            });
    }
});

