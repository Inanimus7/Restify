// register.js
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('formPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const submitBtn = document.getElementById('sign-up-btn');
    const firstNameInput = document.getElementById('firstName'); 
    const lastNameInput = document.getElementById('lastName');   
    
    // Error message elements
    const emailError = document.querySelector('.email-error');
    const passwordError = document.querySelector('.password-error');
    const passwordMatchError = document.querySelector('.passwordMatch-error');
    const formErrorMessage = document.getElementById('formErrorMessage');

    // Email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Function to restrict input to letters only
    function restrictToLetters(input) {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^A-Za-z]/g, '');
        });
    }

    // Apply restriction to firstName and lastName
    restrictToLetters(firstNameInput);
    restrictToLetters(lastNameInput);

    // Form submission handler (unchanged)
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let isValid = true;

        // Reset all error messages
        emailError.classList.add('d-none');
        passwordError.classList.add('d-none');
        passwordMatchError.classList.add('d-none');
        formErrorMessage.classList.add('d-none');

        // Check if all fields are filled
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                formErrorMessage.classList.remove('d-none');
            }
        });

        // Email validation
        if (!emailRegex.test(emailInput.value)) {
            isValid = false;
            emailError.classList.remove('d-none');
        }

        // Password length validation
        if (passwordInput.value.length < 8) {
            isValid = false;
            passwordError.classList.remove('d-none');
        }

        // Password match validation
        if (passwordInput.value !== confirmPasswordInput.value) {
            isValid = false;
            passwordMatchError.classList.remove('d-none');
        }

        // If all validations pass, you can submit the form
        if (isValid) {
            console.log('Form is valid, submitting...');
            form.submit(); 
        }
    });

    // Real-time validation
    emailInput.addEventListener('input', () => {
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailError.classList.remove('d-none');
        } else {
            emailError.classList.add('d-none');
        }
    });

    // Updated password input listener
    passwordInput.addEventListener('input', () => {
        // Check password length
        if (passwordInput.value && passwordInput.value.length < 8) {
            passwordError.classList.remove('d-none');
        } else {
            passwordError.classList.add('d-none');
        }

        // Check password match (only if confirmPassword has a value)
        if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
            passwordMatchError.classList.remove('d-none');
        } else {
            passwordMatchError.classList.add('d-none');
        }
    });

    // Updated confirm password input listener
    confirmPasswordInput.addEventListener('input', () => {
        if (confirmPasswordInput.value && confirmPasswordInput.value !== passwordInput.value) {
            passwordMatchError.classList.remove('d-none');
        } else {
            passwordMatchError.classList.add('d-none');
        }
    });
});

// Password visibility toggle function (unchanged)
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('formPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const checkbox = document.getElementById('showPassword');
    
    if (checkbox.checked) {
        passwordInput.type = 'text';
        confirmPasswordInput.type = 'text';
    } else {
        passwordInput.type = 'password';
        confirmPasswordInput.type = 'password';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formContainer = document.querySelector('.form-container');
        formContainer.classList.add('slide-up');
        
        setTimeout(function() {
            form.submit();
        }, 700); 
    });
});