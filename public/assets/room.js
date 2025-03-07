document.addEventListener("DOMContentLoaded", () => {   
    const stars = document.querySelectorAll(".star");
    const submitButton = document.querySelector('.submit-btn');
    const successMessage = document.getElementById('success-message');
    let selectedRating = null;

    // Star rating functionality
    function resetStars() {
        stars.forEach(star => {
            star.classList.remove("active");
        });
    }

    function highlightStars(index) {
        resetStars();
        for (let i = 0; i <= index; i++) {
            stars[i].classList.add("active");
        }
    }

    stars.forEach((star, index) => {
        star.addEventListener("mouseover", () => {
            highlightStars(index);
        });

        star.addEventListener("mouseout", () => {
            if (selectedRating === null) {
                resetStars();
            } else {
                highlightStars(selectedRating - 1);
            }
        });

        star.addEventListener("click", () => {
            selectedRating = index + 1;
            highlightStars(index);
        });
    });

    // Review submission handler
    submitButton.addEventListener("click", async (e) => {
        e.preventDefault();
        
        const roomId = document.querySelector('input[name="room_id"]').value;

        if (selectedRating !== null) {
            try {
                const response = await fetch('SubmitReview.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `room_id=${encodeURIComponent(roomId)}&rating=${encodeURIComponent(selectedRating)}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    successMessage.textContent = result.message;
                    successMessage.style.backgroundColor = '#d4edda';
                    successMessage.style.color = '#155724';
                    successMessage.style.display = 'block';
                    
                    // Disable further ratings
                    stars.forEach(star => {
                        star.style.pointerEvents = 'none';
                    });
                    submitButton.disabled = true;
                } else {
                    successMessage.textContent = result.message;
                    successMessage.style.backgroundColor = '#f8d7da';
                    successMessage.style.color = '#721c24';
                    successMessage.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                successMessage.textContent = 'An unexpected error occurred';
                successMessage.style.backgroundColor = '#f8d7da';
                successMessage.style.color = '#721c24';
                successMessage.style.display = 'block';
            }
        } else {
            successMessage.textContent = 'Please select a rating before submitting';
            successMessage.style.backgroundColor = '#f8d7da';
            successMessage.style.color = '#721c24';
            successMessage.style.display = 'block';
        }
    });
});