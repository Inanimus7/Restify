// Function to restrict input to alphabets only for first name and last name
const nameFields = document.querySelectorAll('input[name="first_name"], input#lastName');

nameFields.forEach(field => {
    field.addEventListener('input', function (e) {
        // Replace any non-alphabetic character with an empty string
        this.value = this.value.replace(/[^A-Za-z]/g, '');
    });
});

// Slide-in animation for the form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration-form');
    form.style.transform = 'translateX(-100%)';
    setTimeout(() => {
        form.style.transition = 'transform 0.6s ease';
        form.style.transform = 'translateX(0)';
    }, 100);
});

// Toggle password visibility
function togglePasswordVisibility() {
    const password = document.getElementById("formPassword");
    const confirmPassword = document.getElementById("confirmPassword");
    if (password.type === "password") {
        password.type = "text";
        confirmPassword.type = "text";
    } else {
        password.type = "password";
        confirmPassword.type = "password";
    }
}

// Password validation and error display
document.getElementById('submitButton').addEventListener('click', function() {
    const password = document.getElementById('formPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const passwordError = document.querySelector('.password-error');
    const passwordMatchError = document.querySelector('.passwordMatch-error');

    passwordError.style.display = password.length >= 8 ? 'none' : 'block';
    passwordMatchError.style.display = password === confirmPassword ? 'none' : 'block';
});

// Input field label animations
document.querySelectorAll('.input-field input').forEach(input => {
    input.addEventListener('focus', function () {
        const label = this.nextElementSibling;
        label.style.top = '0';
        label.style.fontSize = '12px';
        label.style.color = '#fff'; // Color when focused
        label.style.transform = 'translateY(-16px)';
    });

    input.addEventListener('blur', function () {
        const label = this.nextElementSibling;
        if (!this.value) { // If the input is empty
            label.style.top = '50%';
            label.style.fontSize = '16px';
            label.style.color = '#fff'; // Default label color
            label.style.transform = 'translateY(-50%)';
        }
    });

    // Optional: Ensure label slides up when the input has a value
    input.addEventListener('input', function () {
        const label = this.nextElementSibling;
        if (this.value) {
            label.style.top = '0';
            label.style.fontSize = '12px';
            label.style.color = '#fff';
            label.style.transform = 'translateY(-16px)';
        } else {
            label.style.top = '50%';
            label.style.fontSize = '16px';
            label.style.color = '#fff';
            label.style.transform = 'translateY(-50%)';
        }
    });
});

// Error messages for email, password, and confirm password
document.addEventListener("DOMContentLoaded", function () {
    const emailInput = document.getElementById("emailAddress");
    const passwordInput = document.getElementById("formPassword");
    const confirmPasswordInput = document.getElementById("confirmPassword");

    const emailError = document.querySelector(".email-error");
    const passwordError = document.querySelector(".password-error");
    const passwordMatchError = document.querySelector(".passwordMatch-error");

    // Function to check for a valid email
    function validateEmail() {
        const email = emailInput.value;
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (email === "") {
            emailError.classList.add("d-none");
        } else if (!emailPattern.test(email)) {
            emailError.classList.remove("d-none");
        } else {
            emailError.classList.add("d-none");
        }
    }

    // Function to check if password length is sufficient
    function validatePassword() {
        const password = passwordInput.value;
        if (password === "") {
            passwordError.classList.add("d-none");
        } else if (password.length < 8) {
            passwordError.classList.remove("d-none");
        } else {
            passwordError.classList.add("d-none");
        }
    }

    // Function to check if passwords match
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        if (confirmPassword === "") {
            passwordMatchError.classList.add("d-none");
        } else if (password !== confirmPassword) {
            passwordMatchError.classList.remove("d-none");
        } else {
            passwordMatchError.classList.add("d-none");
        }
    }

    // Event listeners for live validation on typing
    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);
    confirmPasswordInput.addEventListener("input", validatePasswordMatch);
});

// Form validation for submit button (terms checkbox & required checks)
const submitButton = document.getElementById('submitButton');
const agreeTermsCheckbox = document.getElementById('agreeTerms');
const form = document.querySelector('.registration-form');
const inputFields = form.querySelectorAll('input[required]'); // Get all required input fields
const formErrorMessage = document.getElementById('formErrorMessage'); // The error message element

// Function to check if the form is valid
function isFormValid() {
    const allInputsFilled = Array.from(inputFields).every(input => input.value.trim() !== '');
    const isTermsChecked = agreeTermsCheckbox.checked;
    return allInputsFilled && isTermsChecked;
}

// Event listener to monitor form validity
submitButton.addEventListener('click', function (event) {
    if (!isFormValid()) {
        event.preventDefault(); // Prevent form submission if validation fails
        formErrorMessage.classList.remove('d-none');  // Show the error message
    } else {
        formErrorMessage.classList.add('d-none');     // Hide the error message if the form is valid
    }
});

// Optional: Disable the submit button if the form is not valid
agreeTermsCheckbox.addEventListener('change', function () {
    if (isFormValid()) {
        submitButton.disabled = false;  // Enable submit button if form is valid
        formErrorMessage.classList.add('d-none');  // Hide the error message
    } else {
        submitButton.disabled = true;   // Disable submit button if form is invalid
        formErrorMessage.classList.remove('d-none');  // Show the error message
    }
});

// Disable confirm password field if password is empty
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById("formPassword");
    const confirmPasswordInput = document.getElementById("confirmPassword");

    // Function to disable confirm password field if password is empty
    function checkPasswordFields() {
        if (passwordInput.value === "") {
            confirmPasswordInput.disabled = true;  // Disable confirm password field if password is empty
        } else {
            confirmPasswordInput.disabled = false;  // Enable confirm password field if password is not empty
        }
    }

    // Call this function initially in case the user loads the page with an empty password
    checkPasswordFields();

    // Add input event listener to the password field to monitor changes
    passwordInput.addEventListener("input", checkPasswordFields);
});
