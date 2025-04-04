$(document).ready(function() {
    // Initialize the datepicker with settings
    $("#check-in-date, #check-out-date").datepicker({
        dateFormat: "dd/mm/yy", 
        showAnim: "fadeIn",
        changeMonth: true,
        changeYear: true,
        minDate: 0,  
        maxDate: "+1Y",  

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

            // Handle date selection: Adjust the other date field accordingly
            handleDateSelection(id, selectedDate);
        }
    });

    // Handler function to manage date selection
    function handleDateSelection(selectedId, selectedDate) {
        const selectedDateObject = $.datepicker.parseDate('dd/mm/yy', selectedDate);

        // If the user selects a check-in date
        if (selectedId === "check-in-date") {
            // Adjust the minDate for check-out
            $("#check-out-date").datepicker("option", "minDate", selectedDateObject);

            // If check-out date is set, but is earlier than check-in, reset it
            if ($("#check-out-date").val() && $.datepicker.parseDate('dd/mm/yy', $("#check-out-date").val()) < selectedDateObject) {
                $("#check-out-date").val("");  
            }

        } else if (selectedId === "check-out-date") {
            // If the user selects a check-out date, adjust the maxDate for check-in
            $("#check-in-date").datepicker("option", "maxDate", selectedDateObject);

            // If check-in date is set, but is later than check-out, reset it
            if ($("#check-in-date").val() && $.datepicker.parseDate('dd/mm/yy', $("#check-in-date").val()) > selectedDateObject) {
                $("#check-in-date").val("");  
            }
        }

        // Reset check-in or check-out only if BOTH are set
        if ($("#check-in-date").val() && $("#check-out-date").val()) {
            // Check if check-out date is earlier than check-in date (invalid case)
            const checkInDateObject = $.datepicker.parseDate('dd/mm/yy', $("#check-in-date").val());
            const checkOutDateObject = $.datepicker.parseDate('dd/mm/yy', $("#check-out-date").val());

            if (checkOutDateObject < checkInDateObject) {
                // If check-out is before check-in, reset both
                $("#check-in-date").val("");
                $("#check-out-date").val("");
            }
        }
    }

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
        
        $("#date-error-message").hide();
    });
    
    // Also hide the error message when the user starts selecting dates
    $("#check-in-date, #check-out-date").on("focus", function() {
        $("#date-error-message").hide();
    });
});
$(document).ready(function() {
    $('#search-btn').click(function(event) {
        var checkInDate = $('#check-in-date').val();
        var checkOutDate = $('#check-out-date').val();

        // Check if the dates are invalid
        if (!checkInDate || !checkOutDate || checkInDate >= checkOutDate) {
            // Show the error message if dates are invalid
            $('#date-error-message').show();
            $('#date-error-message').addClass('shake'); 

            // Remove the shake class after animation ends
            setTimeout(function() {
                $('#date-error-message').removeClass('shake');
            }, 500); 
        }
    });
});