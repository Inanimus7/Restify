<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// Autoload classes
spl_autoload_register(function ($class) {
    $class = str_replace("\\", "/", $class);
    require_once sprintf(__DIR__ . '/../app/%s.php', $class);
});

// Use statements
use Hotel\User;
use Hotel\Room;

// Instantiate User class
$user = new User();

// Check if there is a token given
$userToken = $_COOKIE['user_token'];
if ($userToken) {
    // Verify user
    if ($user->verifyToken($userToken)) {
        // Set user in memory 
        $userInfo = $user->getTokenPayload($userToken);
        User::setCurrentUserId($userInfo['user_id']);
    }
}

// Instantiate Room class
$room = new Room();

// Get the list of cities from the database
$cities = $room->getCities(); 

// Get input parameters from the GET request
$city = $_GET['city'] ?? null;
$typeId = $_GET['room_type'] ?? null;
$checkInDate = $_GET['check_in_date'] ?? null;
$checkOutDate = $_GET['check_out_date'] ?? null;
// Room type mapping
$roomTypeMapping = [
    'Single Room' => 1,
    'Double Room' => 2,
    'Triple Room' => 3,
    'Fourfold Room' => 4
];

// Get room type from the form and map it to the corresponding ID
$roomTypeName = $_GET['room_type'] ?? null;
$roomTypeId = $roomTypeMapping[$roomTypeName] ?? null;

// Check if all required parameters are provided
if ($city && $roomTypeId && $checkInDate && $checkOutDate) {
    // Convert check-in and check-out dates to DateTime objects
    $checkInDate = new DateTime($checkInDate);
    $checkOutDate = new DateTime($checkOutDate);

    // Call the search method to get rooms
    $rooms = $room->search($city, $roomTypeId, $checkInDate, $checkOutDate);

    // Output the rooms (just for debugging, remove later)
    print_r($rooms);
}
?>
