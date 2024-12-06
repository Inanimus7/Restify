window.addEventListener("DOMContentLoaded", function () {
    // Retrieve values from localStorage
    const destination = localStorage.getItem("destination");
    const checkInDate = localStorage.getItem("checkInDate");
    const checkOutDate = localStorage.getItem("checkOutDate");
    const guests = localStorage.getItem("guests");

    // Update the elements by adding the values, preserving existing HTML structure
    if (destination) {
        document.querySelector(".destination").innerHTML = `<i class="fa-solid fa-location-dot"></i> ${destination}`;
    }
    if (checkInDate) {
        document.querySelector(".check-in-date").innerHTML = `<i class="fas fa-calendar-check"></i> ${checkInDate}`;
    }
    if (checkOutDate) {
        document.querySelector(".check-out-date").innerHTML = `<i class="fas fa-calendar-times"></i> ${checkOutDate}`;
    }
    if (guests) {
        document.querySelector(".no-guests").innerHTML = `<i class="fa-solid fa-users"></i> ${guests}`;
    }
});
