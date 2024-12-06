document.addEventListener("DOMContentLoaded", () => {
  const stars = document.querySelectorAll(".star");
  let selectedRating = null; // To track the user's selected rating
  
  // Function to reset all stars to their default state
  function resetStars() {
      stars.forEach(star => {
          star.classList.remove("active"); // Remove the 'active' class
      });
  }

  // Function to highlight stars up to a certain index (used for hover and click)
  function highlightStars(index) {
      resetStars(); // Reset all stars
      for (let i = 0; i <= index; i++) {
          stars[i].classList.add("active"); // Add the 'active' class to the selected stars
      }
  }

  // Hover event for each star
  stars.forEach((star, index) => {
      star.addEventListener("mouseover", () => {
          highlightStars(index); // Highlight stars up to the hovered one
      });

      // Reset stars when the mouse leaves the star rating area
      star.addEventListener("mouseout", () => {
          if (selectedRating === null) {
              resetStars(); // Reset the stars if no rating has been selected
          } else {
              highlightStars(selectedRating - 1); // Show the selected rating (index starts from 0)
          }
      });

      // Click event to set the rating
      star.addEventListener("click", () => {
          selectedRating = index + 1; // Store the rating (index + 1 because index starts from 0)
          highlightStars(index); // Highlight up to the clicked star
      });
  });

  // Handle the submit button
  const submitButton = document.querySelector('.submit-btn');
  submitButton.addEventListener("click", (e) => {
      e.preventDefault(); // Prevent form submission for testing
      if (selectedRating !== null) {
          console.log("Rating submitted:", selectedRating);
          // Here, you can send the selectedRating value to the server or handle it as needed
      } else {
          console.log("Please select a rating before submitting.");
      }
  });
});
