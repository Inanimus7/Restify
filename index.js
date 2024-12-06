$(document).ready(function () {
    // Initialize datepicker with toggle functionality for the Check-In and Check-Out inputs
    let toggleDatepicker = {
        "check-in-date": true,
        "check-out-date": true
    };

    // Initialize datepicker with specific ID selectors to avoid conflicts
    $("#check-in-date, #check-out-date").datepicker({
        dateFormat: "dd/mm/yy",
        minDate: 0, // Disallow past dates
        beforeShow: function (input, inst) {
            const id = $(input).attr("id");

            // If the input already has a value, always allow reopening the datepicker
            if ($(input).val()) {
                toggleDatepicker[id] = true;
            }

            return true; // Ensure the datepicker opens every time
        },
        onSelect: function (selectedDate) {
            const id = $(this).attr("id");
            const date = $(this).datepicker("getDate");

            if (id === "check-in-date") {
                $("#check-out-date").datepicker("option", "minDate", date);
            } else {
                $("#check-in-date").datepicker("option", "maxDate", date);
            }
        }
    });

    // Reset the datepicker when clicking again (focus event) to change the date
    $('#check-in-date, #check-out-date').focus(function () {
        // Reset datepicker toggle to true when the input is focused
        const id = $(this).attr('id');
        toggleDatepicker[id] = true;
    });
});

// Initialize the Check-In Datepicker
$(document).ready(function () {
    $("#check-in-date").datepicker({
        dateFormat: "dd/mm/yy",  
        showAnim: "fadeIn",      
        changeMonth: true,       
        changeYear: true,        
        minDate: 0,              
        maxDate: "+1Y",          
    });

    // Initialize the Check-Out Datepicker
    $("#check-out-date").datepicker({
        dateFormat: "dd/mm/yy",  
        showAnim: "fadeIn",      
        changeMonth: true,       
        changeYear: true,        
        minDate: 0,              
        maxDate: "+1Y",          
    });
});

// Initialize autocomplete for the destination input field
$(document).ready(function () {
    const cities = [
        "Athens", "Thessaloniki", "Patras", "Heraklion", "Larissa",
        "Volos", "Ioannina", "Chania", "Rhodes", "Kavala", "Corfu",
        "Kalamata", "Katerini", "Komotini", "Tripoli", "Serres", "Xanthi",
        "Chalcis", "Kozani", "Alexandroupoli", "Lamia", "Rethymno",
        "Mytilene", "Chios", "Kilkis", "Thebes", "Sparta", "Piraeus",
        "Patmos", "Nafplio", "Kastoria", "Arta", "Aigio", "Drama", "Glyfada",
        "Kavala", "Corinth", "Megara", "Chalkida", "Amaliada", "Preveza",
        "Aigina", "Samos", "Lefkada", "Tinos", "Milos", "Kefalonia", "Santorini",
        "Mykonos", "Naxos", "Paros", "Ios", "Folegandros", "Skiathos", "Skopelos",
        "Syros", "Andros", "Kithira", "Kalymnos", "Kos", "Leros", "Symi", "Hydra",
        "Spetses", "Kalamata", "Chios", "Zakynthos", "Lefkada", "Thasos", "Samos",
        "Alexandria", "Kalamaria", "Alimos", "Nea Smyrni"
    ];
    
    // Append ", Greece" to each city name
    const citiesInGreece = cities.map(city => `${city}, Greece`);
    
    console.log(citiesInGreece);
    

    // Initialize autocomplete on the destination input
    $("#destination").autocomplete({
        source: citiesInGreece,  // Provide the array as the source
        minLength: 2,  // Start suggesting when 2 characters are typed
    });
});
document.getElementById("search-btn").addEventListener("click", function () {
    // Get values from the form inputs
    const destination = document.getElementById("destination").value;
    const checkInDate = document.getElementById("check-in-date").value;
    const checkOutDate = document.getElementById("check-out-date").value;
    const guests = document.getElementById("guests").value;

    // Store values in localStorage
    localStorage.setItem("destination", destination);
    localStorage.setItem("checkInDate", checkInDate);
    localStorage.setItem("checkOutDate", checkOutDate);
    localStorage.setItem("guests", guests);

    // Redirect to the second HTML page
    window.location.href = "search.html";
});