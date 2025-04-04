<?php
use Hotel\User;
use Hotel\Booking;

// Include bootstrap file
require_once __DIR__ .'/../../boot/boot.php';

// Check if request method is POST
if(strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
    header('Location: /Project/public/assets/login.php');
    return;
}

// If no user is logged in, return to main page
if (empty(User::getCurrentUserId())) {
    header('Location: /');
    return;
}

// Check if room id is given
$roomId = $_REQUEST['room_id'];
if (empty($roomId)) {
    header('Location: /');
    return;
}

// Get the dates (already in Y-m-d format from the hidden form fields)
$checkInDate = $_REQUEST['check_in_date'];
$checkOutDate = $_REQUEST['check_out_date'];

// Create Booking
$booking = new Booking();
$success = $booking->bookNow($roomId, User::getCurrentUserId(), $checkInDate, $checkOutDate);

// Redirect based on result
if ($success) {
    header('Location: /Project/public/assets/room.php?room_id=' . $roomId . '&booking=success');
} else {
    header('Location: /Project/public/assets/room.php?room_id=' . $roomId . '&booking=error');
}