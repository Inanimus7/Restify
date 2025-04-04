document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.querySelector('.email-error');
    const formError = document.querySelector('.form-error');
    const submitBtn = document.getElementById('login-btn');

    // Email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Check form validity for real-time hiding of form-error
    const checkFormValidity = () => {
        const isEmailFilled = emailInput.value.trim() !== '';
        const isPasswordFilled = passwordInput.value.trim() !== '';
        if (isEmailFilled && isPasswordFilled) {
            formError.classList.add('d-none');
        }
    };

    // Email validation on blur (show error if invalid)
    emailInput.addEventListener('blur', () => {
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailError.classList.remove('d-none');
        } else {
            emailError.classList.add('d-none');
        }
    });

    // Real-time email error hiding while typing
    emailInput.addEventListener('input', () => {
        if (emailInput.value && emailRegex.test(emailInput.value)) {
            emailError.classList.add('d-none'); // Hide if valid
        }
        checkFormValidity(); // Still check form-error
    });

    // Real-time check for form-error hiding
    passwordInput.addEventListener('input', checkFormValidity);

    // Form submission handling
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const isEmailValid = emailRegex.test(emailInput.value);
        const isPasswordFilled = passwordInput.value.trim() !== '';

        if (!emailInput.value || !isPasswordFilled) {
            formError.classList.remove('d-none');
            emailError.classList.add('d-none');
        } else if (!isEmailValid) {
            emailError.classList.remove('d-none');
            formError.classList.add('d-none');
        } else {
            emailError.classList.add('d-none');
            formError.classList.add('d-none');
            form.submit();
        }
    });
});

// Password visibility toggle
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const checkbox = document.getElementById('showPassword');
    if (checkbox.checked) {
        passwordInput.type = 'text';
    } else {
        passwordInput.type = 'password';
    }
}