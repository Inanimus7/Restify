const navItems = document.querySelectorAll('.user-options li');

const handleNavClick = (item) => {
    const targetId = item.getAttribute('data-target');
    const targetContent = document.getElementById(targetId);

    // Remove active class from all items
    navItems.forEach(nav => nav.classList.remove('active'));

    // Add active class to the clicked item
    item.classList.add('active');

    // Hide all sections
    const allSections = document.querySelectorAll('.content-section');
    allSections.forEach(section => {
        section.style.display = 'none';
    });

    // Show the target section with appropriate display
    if (targetId === 'personal-info') {
        targetContent.style.display = 'block'; 
    } else if (targetId === 'booking-history') {
        targetContent.style.display = 'flex'; 
    } else if (targetId === 'user-favorites') {
        targetContent.style.display = 'block'; 
    } else if (targetId === 'user-reviews') {
        targetContent.style.display = 'block'; 
    }
};

// Add click event listeners
navItems.forEach(item => {
    item.addEventListener('click', () => handleNavClick(item));
});

// On page load, show Personal Info and set it as active
document.addEventListener('DOMContentLoaded', () => {
    const personalInfoItem = document.querySelector('[data-target="personal-info"]');
    if (personalInfoItem) {
        handleNavClick(personalInfoItem); 
    }
});
// Image zoom functionality with close button for profile.js
document.addEventListener('DOMContentLoaded', function() {
    // Create overlay element once
    const overlay = document.createElement('div');
    overlay.className = 'zoom-overlay';
    document.body.appendChild(overlay);
    
    // Add click event on booking images
    const setupZoomableImages = () => {
        // Select all booking images
        const images = document.querySelectorAll('.booking-image img');
        
        images.forEach(img => {
            img.style.cursor = 'pointer'; 
            
            img.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Create container for image and close button
                const imageContainer = document.createElement('div');
                imageContainer.className = 'zoomed-image-container';
                
                // Create close button
                const closeBtn = document.createElement('button');
                closeBtn.className = 'zoom-close-btn';
                closeBtn.innerHTML = '&times;'; 
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    overlay.classList.remove('active');
                });
                
                // Create zoomed image
                const zoomedImg = document.createElement('img');
                zoomedImg.src = this.src;
                zoomedImg.className = 'zoomed-image';
                
                // Add elements to container
                imageContainer.appendChild(zoomedImg);
                imageContainer.appendChild(closeBtn);
                
                // Clear overlay and add the new container
                overlay.innerHTML = '';
                overlay.appendChild(imageContainer);
                
                // Show overlay
                overlay.classList.add('active');
            });
        });
    };
    
    // Close overlay when clicking outside the image
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.classList.remove('active');
        }
    });
    
    // Add ESC key support to close the overlay
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            overlay.classList.remove('active');
        }
    });
    
    // Setup images on load
    setupZoomableImages();
});

document.addEventListener('DOMContentLoaded', function() {
    // Function to handle alert messages
    const handleAlerts = () => {
        // Find all alert messages
        const alerts = document.querySelectorAll('.alert');
        
        if (alerts.length > 0) {
            alerts.forEach(alert => {
                const alertId = 'alert-' + Date.now();
                alert.setAttribute('data-alert-id', alertId);
                
                // Set a timeout to fade out the alert
                setTimeout(() => {
                    // Add a fade-out class
                    alert.classList.add('fade-out');
                    
                    // Remove the alert after the animation completes
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500); 
                }, 5000); 
                
                // Add a close button (optional)
                const closeBtn = document.createElement('button');
                closeBtn.innerHTML = '&times;';
                closeBtn.className = 'alert-close-btn';
                closeBtn.addEventListener('click', () => {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                });
                alert.appendChild(closeBtn);
            });
            
            // Add to session storage to prevent showing again on reload
            sessionStorage.setItem('alertsShown', 'true');
        }
    };
    if (!sessionStorage.getItem('alertsShown')) {
        handleAlerts();
    } else {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        });
    }
    
    // Clear the session storage when navigating away from the page
    window.addEventListener('beforeunload', () => {
        sessionStorage.removeItem('alertsShown');
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Select all toggle password icons
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');
    
    // Add click event listener to each icon
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling.previousElementSibling;
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
                this.setAttribute('title', 'Hide Password');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
                this.setAttribute('title', 'Show Password');
            }
        });
    });

    // Password length validation
    const form = document.querySelector('.password-form'); 
    const passwordInput = document.getElementById('new_password'); 
    const passwordError = document.querySelector('.password-error'); 

    // Form submission handler
    if (form) {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            // Reset error message
            passwordError.classList.add('d-none');
            // Password length validation
            if (passwordInput.value.length < 8) {
                e.preventDefault(); 
                isValid = false;
                passwordError.classList.remove('d-none');
            }
        });
    }

    // Real-time input validation
    if (passwordInput && passwordError) {
        passwordInput.addEventListener('input', () => {
            if (passwordInput.value && passwordInput.value.length < 8) {
                passwordError.classList.remove('d-none');
            } else {
                passwordError.classList.add('d-none');
            }
        });
    }
});
