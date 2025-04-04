$(document).ready(function() {
    // Default values for check-in and check-out dates (empty strings)
    const defaultCheckInDate = "";
    const defaultCheckOutDate = "";

    // Initialize the datepicker with settings
    $("#check-in-date, #check-out-date").datepicker({
        dateFormat: "dd/mm/yy", 
        showAnim: "fadeIn",
        changeMonth: true,
        changeYear: true,
        minDate: 0,  // Disallow past dates
        maxDate: "+1Y",  // Allow dates up to one year from now

        beforeShow: function(input, inst) {
            const id = $(input).attr("id");

            // If the input already has a value, allow reopening the datepicker
            if ($(input).val()) {
                // Dynamically adjust the min/max dates
                handleDateSelection(id, $(input).val());
            }
            return true;
        },

        onSelect: function(selectedDate) {
            const id = $(this).attr("id");
            const selectedDateObject = $(this).datepicker("getDate");

            // Handle date selection: Adjust the other date field accordingly
            handleDateSelection(id, selectedDate);

            // Keep the datepicker open to allow continuous selection
            $(this).datepicker("show");
        }
    });

    // Handler function to manage date selection
    function handleDateSelection(selectedId, selectedDate) {
        const selectedDateObject = $.datepicker.parseDate('dd/mm/yy', selectedDate);

        if (selectedId === "check-in-date") {
            // If check-in date is selected, adjust the minDate for check-out
            $("#check-out-date").datepicker("option", "minDate", selectedDateObject);
            // Reset the check-out date if the check-in date changes
            if ($("#check-out-date").val() && $.datepicker.parseDate('dd/mm/yy', $("#check-out-date").val()) < selectedDateObject) {
                $("#check-out-date").val("");  // Clear the check-out date if it's invalid
            }
        } else if (selectedId === "check-out-date") {
            // If check-out date is selected, adjust the maxDate for check-in
            $("#check-in-date").datepicker("option", "maxDate", selectedDateObject);
            // Reset the check-in date if the check-out date changes
            if ($("#check-in-date").val() && $.datepicker.parseDate('dd/mm/yy', $("#check-in-date").val()) > selectedDateObject) {
                $("#check-in-date").val("");  // Clear the check-in date if it's invalid
            }
        }
    }    
    
    // Add form validation for search
    $(".filter-form").on("submit", function(e) {
        // Validate check-in and check-out dates
        const checkInDate = $("#check-in-date").val();
        const checkOutDate = $("#check-out-date").val();
        
        if (!checkInDate || !checkOutDate) {
            e.preventDefault();
            $("#date-error-message").show();
            return false;
        }
        
        // Hide error message if dates are valid
        $("#date-error-message").hide();
    });
});

$(document).ready(function() {
    
    // Add form validation
    $(".filter-form").on("submit", function(e) {
        // Get the values of check-in and check-out dates
        const checkInDate = $("#check-in-date").val();
        const checkOutDate = $("#check-out-date").val();
        
        // Check if either date is missing
        if (!checkInDate || !checkOutDate) {
            // Prevent the form from submitting
            e.preventDefault();
            
            // Show the error message
            $("#date-error-message").show();
            
            // Scroll to the error message to make sure it's visible
            $('html, body').animate({
                scrollTop: $("#date-error-message").offset().top - 100
            }, 200);
            
            return false;
        }
        
        // If we reach here, both dates are provided, hide any error message
        $("#date-error-message").hide();
    });
    
    // Also hide the error message when the user starts selecting dates
    $("#check-in-date, #check-out-date").on("focus", function() {
        $("#date-error-message").hide();
    });
});

// Image zoom functionality for list.php
document.addEventListener('DOMContentLoaded', function() {
    // Create overlay element once
    const overlay = document.createElement('div');
    overlay.className = 'zoom-overlay';
    document.body.appendChild(overlay);
    
    // Initialize zoom functionality
    initializeZoom();
    
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
});

// Reusable function to initialize zoom functionality
function initializeZoom() {
    // Find all room image divs
    const roomImageDivs = document.querySelectorAll('.results-img');
    const overlay = document.querySelector('.zoom-overlay');
    
    // Set up each image div for zooming
    roomImageDivs.forEach(div => {
        div.style.cursor = 'pointer'; // Show pointer cursor on hover
        
        // Remove any existing click event listeners
        const newDiv = div.cloneNode(true);
        div.parentNode.replaceChild(newDiv, div);
        
        // Add click event listener
        newDiv.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Extract the image URL from the background-image CSS property
            const style = window.getComputedStyle(this);
            const bgImage = style.backgroundImage;
            
            // Extract the URL from the "url('...')" string
            const url = bgImage.replace(/^url\(['"](.+)['"]\)/, '$1');
            
            // Create zoomed image
            const zoomedImg = document.createElement('img');
            zoomedImg.src = url;
            zoomedImg.className = 'zoomed-image';
            
            // Clear overlay and add the new image
            overlay.innerHTML = '';
            overlay.appendChild(zoomedImg);
            
            // Show overlay
            overlay.classList.add('active');
        });
    });
}

const rangevalue = document.querySelector(".slider .price-slider");
const rangeInputvalue = document.querySelectorAll(".range-input input");

// Set the price gap
let priceGap = 10;  

// Adding event listeners to price input elements
const priceInputvalue = document.querySelectorAll(".price-input input");
for (let i = 0; i < priceInputvalue.length; i++) {
    priceInputvalue[i].addEventListener("input", e => {

        // Parse min and max values of the range input
        let minp = parseInt(priceInputvalue[0].value);
        let maxp = parseInt(priceInputvalue[1].value);
        let diff = maxp - minp;

        if (minp < 0) {
            alert("Minimum price cannot be less than 0");
            priceInputvalue[0].value = 0;
            minp = 0;
        }

        // Validate the input values
        if (maxp > 500) {
            alert("Maximum price cannot be greater than 500");
            priceInputvalue[1].value = 500;
            maxp = 500;
        }

        if (minp > maxp - priceGap) {
            priceInputvalue[0].value = maxp - priceGap;
            minp = maxp - priceGap;

            if (minp < 0) {
                priceInputvalue[0].value = 0;
                minp = 0;
            }
        }

        // Check if the price gap is met and max price is within the range
        if (diff >= priceGap && maxp <= rangeInputvalue[1].max) {
            if (e.target.className === "min-input") {
                rangeInputvalue[0].value = minp;
                let value1 = rangeInputvalue[0].max;
                rangevalue.style.left = `${(minp / value1) * 100}%`; 
            }
            else {
                rangeInputvalue[1].value = maxp;
                let value2 = rangeInputvalue[1].max;
                rangevalue.style.right = `${100 - (maxp / value2) * 100}%`;  
            }
        }
    });

    // Add event listeners to range input elements
    for (let i = 0; i < rangeInputvalue.length; i++) {
        rangeInputvalue[i].addEventListener("input", e => {
            let minVal = parseInt(rangeInputvalue[0].value);
            let maxVal = parseInt(rangeInputvalue[1].value);

            let diff = maxVal - minVal;

            // Check if the price gap is exceeded
            if (diff < priceGap) {

                // Check if the input is the min range input
                if (e.target.className === "min-range") {
                    rangeInputvalue[0].value = maxVal - priceGap;
                }
                else {
                    rangeInputvalue[1].value = minVal + priceGap;
                }
            }
            else {

                // Update price inputs and range progress
                priceInputvalue[0].value = minVal;
                priceInputvalue[1].value = maxVal;
                rangevalue.style.left = `${(minVal / rangeInputvalue[0].max) * 100}%`; 
                rangevalue.style.right = `${100 - (maxVal / rangeInputvalue[1].max) * 100}%`;  
            }
        });
    }
}

$(document).ready(function() {
    const minPriceInput = $('.min-input');
    const maxPriceInput = $('.max-input');
    const minRange = $('.min-range');
    const maxRange = $('.max-range');
    const priceSlider = $('.price-slider');
    const priceGap = 10;

    // Function to update slider progress bar
    function updateSliderProgress(minVal, maxVal) {
        const minPercent = (minVal / 500) * 100;
        const maxPercent = 100 - (maxVal / 500) * 100;
        priceSlider.css({
            'left': `${minPercent}%`,
            'right': `${maxPercent}%`
        });
    }

    // Function to update price range display
    function updatePriceDisplay(minVal, maxVal) {
        $('#priceRangeDisplay').text(`${minVal} - ${maxVal}`);
    }

    // Sync range sliders to number inputs
    minRange.on('input', function() {
        let minVal = parseInt($(this).val());
        let maxVal = parseInt(maxRange.val());

        if (minVal > maxVal - priceGap) {
            minVal = maxVal - priceGap;
            $(this).val(minVal);
        }
        if (minVal < 0) minVal = 0;

        minPriceInput.val(minVal);
        updateSliderProgress(minVal, maxVal);
        updatePriceDisplay(minVal, maxVal);
    });

    maxRange.on('input', function() {
        let minVal = parseInt(minRange.val());
        let maxVal = parseInt($(this).val());

        if (maxVal < minVal + priceGap) {
            maxVal = minVal + priceGap;
            $(this).val(maxVal);
        }
        if (maxVal > 500) maxVal = 500;

        maxPriceInput.val(maxVal);
        updateSliderProgress(minVal, maxVal);
        updatePriceDisplay(minVal, maxVal);
    });

    // Sync number inputs to range sliders
    minPriceInput.on('input', function() {
        let minVal = parseInt($(this).val()) || 0;
        let maxVal = parseInt(maxPriceInput.val());

        if (minVal < 0) minVal = 0;
        if (minVal > maxVal - priceGap) minVal = maxVal - priceGap;

        $(this).val(minVal);
        minRange.val(minVal);
        updateSliderProgress(minVal, maxVal);
        updatePriceDisplay(minVal, maxVal);
    });

    maxPriceInput.on('input', function() {
        let minVal = parseInt(minPriceInput.val());
        let maxVal = parseInt($(this).val()) || 500;

        if (maxVal > 500) maxVal = 500;
        if (maxVal < minVal + priceGap) maxVal = minVal + priceGap;

        $(this).val(maxVal);
        maxRange.val(maxVal);
        updateSliderProgress(minVal, maxVal);
        updatePriceDisplay(minVal, maxVal);
    });

    // Initial setup
    const initialMin = parseInt(minPriceInput.val());
    const initialMax = parseInt(maxPriceInput.val());
    minRange.val(initialMin);
    maxRange.val(initialMax);
    updateSliderProgress(initialMin, initialMax);
    updatePriceDisplay(initialMin, initialMax);
});