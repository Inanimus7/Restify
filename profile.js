// Select all nav items and the main-content area
const navItems = document.querySelectorAll('.user-options li');
const mainContent = document.querySelector('.main-content');

// Add click event listeners to each navigation item
navItems.forEach(item => {
    item.addEventListener('click', () => {
        const targetId = item.getAttribute('data-target'); // Get the target content ID
        const targetContent = document.getElementById(targetId); // Retrieve the content section

        // Hide all content sections first
        const allSections = document.querySelectorAll('.content-section');
        allSections.forEach(section => {
            section.style.display = 'none';
        });

        // Show the target content section
        targetContent.style.display = 'block';
    });
});
// Get references to the necessary elements
const fileInput = document.getElementById('file-upload');
const profileImage = document.getElementById('profile-image');
const fallbackIcon = document.getElementById('fallback-icon');

// Add an event listener to handle the image upload
fileInput.addEventListener('change', function(event) {
    const file = event.target.files[0];  // Get the selected file

    if (file) {
        const reader = new FileReader();

        // When the file is successfully read
        reader.onload = function(e) {
            // Hide the fallback icon and show the profile image
            fallbackIcon.style.display = 'none';  // Hide the user icon
            profileImage.style.display = 'block';  // Show the profile image

            // Set the uploaded image as the source
            profileImage.src = e.target.result;  // Use the file reader result as the image source
        };

        // Read the uploaded file as a data URL
        reader.readAsDataURL(file);
    }
});