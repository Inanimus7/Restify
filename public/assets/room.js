$(document).ready(function() {
    // Handle star rating selection
    $('.rating label').on('click', function() {
        $(this).siblings('input').prop('checked', true);
        $('.rating label').removeClass('selected');
        $(this).addClass('selected');
        $(this).prevAll('label').addClass('selected');
    });

    // Handle review form submission
    $('#review-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: '../actions/review.php',
            type: 'POST',
            data: formData,
            dataType: 'json', 
            success: function(response) {
                if (response.status === 'success') {
                    $('#review-success-message').fadeIn(); 
                    $('#reviewField').val('');
                    $('.rating input').prop('checked', false);
                    $('.rating label').removeClass('selected');
                    // Append new review dynamically
                    $('.reviews-section').append(response.review_html);
                    setTimeout(function() {
                        $('#review-success-message').fadeOut();
                    }, 3000); 
                }
            }
        });
    });

    // Handle booking success message
    console.log('Page loaded, checking for booking success message');
    if ($('#booking-success-message').length) { // Correct ID
        console.log('Booking success message found, starting fade');
        setTimeout(function() {
            $('#booking-success-message').fadeOut(1000, function() {
                console.log('Booking success message faded out, showing booked message');
                $('.booked-message').fadeIn(500);
            });
        }, 2000);
    } else {
        console.log('No booking success message found');
    }

    // Google Maps initialization function (commented out to prevent interference)
    /*
    window.initMap = function() {
        // Get latitude and longitude from data attributes
        const latitude = parseFloat($('.room-location').data('lat'));
        const longitude = parseFloat($('.room-location').data('lng'));
        
        console.log('Initializing map with coordinates:', latitude, longitude);
        
        // Create a map centered at the hotel location
        const hotelLocation = { lat: latitude, lng: longitude };
        const map = new google.maps.Map($('.room-location')[0], {
            zoom: 15,
            center: hotelLocation,
            mapTypeControl: true,
            scrollwheel: false,
            streetViewControl: true
        });
        
        // Add a marker for the hotel
        const marker = new google.maps.Marker({
            position: hotelLocation,
            map: map,
            title: $('.room-location').data('hotel-name')
        });
        
        // Create info window with hotel details
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; max-width: 200px;">
                    <h4 style="margin-top: 0; margin-bottom: 8px;">${$('.room-location').data('hotel-name')}</h4>
                    <p style="margin: 0;">${$('.room-location').data('address')}</p>
                </div>
            `
        });
        
        // Open info window when marker is clicked
        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
        
        // Show the info window by default
        infoWindow.open(map, marker);
    };
    
    // Load the Google Maps API if the map container exists
    if ($('.room-location').length) {
        // Add the script tag dynamically
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
    */
});