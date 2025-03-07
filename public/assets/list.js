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

document.addEventListener("DOMContentLoaded", function () {
    const minPriceRange = document.getElementById("minPriceRange");
    const maxPriceRange = document.getElementById("maxPriceRange");
    const minRangeInput = document.getElementById("minRangeInput");
    const maxRangeInput = document.getElementById("maxRangeInput");
    const priceRangeDisplay = document.getElementById("priceRangeDisplay");

    // Create hidden input fields for form submission if they don't exist
    function createHiddenInputs() {
        const form = document.querySelector(".filter-form");
        
        // Check if hidden inputs already exist, if not create them
        if (!document.querySelector('input[name="minPrice"]')) {
            const minPriceInput = document.createElement("input");
            minPriceInput.type = "hidden";
            minPriceInput.name = "minPrice";
            minPriceInput.value = minPriceRange.value;
            form.appendChild(minPriceInput);
        }
        
        if (!document.querySelector('input[name="maxPrice"]')) {
            const maxPriceInput = document.createElement("input");
            maxPriceInput.type = "hidden";
            maxPriceInput.name = "maxPrice";
            maxPriceInput.value = maxPriceRange.value;
            form.appendChild(maxPriceInput);
        }
    }

    // Function to update price range display and hidden inputs
    function updateRangeValues() {
        let minValue = parseInt(minPriceRange.value);
        let maxValue = parseInt(maxPriceRange.value);

        // Ensure the minimum value is less than the maximum value
        if (maxValue - minValue < 10) {
            minPriceRange.value = maxValue - 10;
            minValue = parseInt(minPriceRange.value);
        }

        // Update input fields with euro symbol
        minRangeInput.value = minValue + " €";
        maxRangeInput.value = maxValue + " €";

        // Update the displayed price range in the sidebar
        if (priceRangeDisplay) {
            priceRangeDisplay.textContent = minValue + " € - " + maxValue + " €";
        }
        
        // Update hidden form fields
        const minPriceInput = document.querySelector('input[name="minPrice"]');
        const maxPriceInput = document.querySelector('input[name="maxPrice"]');
        
        if (minPriceInput) minPriceInput.value = minValue;
        if (maxPriceInput) maxPriceInput.value = maxValue;
    }

    // Create hidden inputs when page loads
    createHiddenInputs();

    // Event listeners to update values as the user drags the sliders
    minPriceRange.addEventListener("input", updateRangeValues);
    maxPriceRange.addEventListener("input", updateRangeValues);

    // Initialize the slider values on page load
    updateRangeValues();
});

$(document).ready(function() {
    // Your existing datepicker code remains here...
    
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
