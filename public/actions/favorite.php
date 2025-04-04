<?php
// Load required files and initialize Favorite service
require 'C:/xampp/htdocs/vhosts/hotel.collegelink.localhost/Project/boot/boot.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Hotel\Favorite;
use Hotel\User;

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An error occurred'
];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user from cookie
    $user = new User();
    $userId = null;
    
    if (isset($_COOKIE['user_token'])) {
        $token = $_COOKIE['user_token'];
        $userId = $user->getUserFromToken($token);
    }
    
    // Check if user is logged in
    if (!$userId) {
        $response['message'] = 'Please log in to add favorites';
        header('Location: ../assets/login.php');
        exit;
    }
    
    // Get room ID and action from POST data
    $roomId = isset($_POST['room_id']) ? $_POST['room_id'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    $checkInDate = isset($_POST['check_in_date']) ? $_POST['check_in_date'] : null;
    $checkOutDate = isset($_POST['check_out_date']) ? $_POST['check_out_date'] : null;
    
    // Validate room ID
    if (!$roomId || !is_numeric($roomId) || $roomId <= 0) {
        $response['message'] = 'Invalid room ID';
        header('Location: ../assets/list.php');
        exit;
    }
    
    // Initialize Favorite service
    $favorite = new Favorite();

    if ($action === 'add') {
        $result = $favorite->addFavorite($roomId, $userId);
        error_log("Add Favorite - Room: $roomId, User: $userId, Result: " . var_export($result, true));
    } else if ($action === 'remove') {
        $result = $favorite->removeFavorite($roomId, $userId);
        error_log("Remove Favorite - Room: $roomId, User: $userId, Result: " . var_export($result, true));
    }

    // Build redirect URL with room_id and dates
    $redirectUrl = "../assets/room.php?room_id=" . urlencode($roomId);
    if ($checkInDate) {
        $redirectUrl .= "&check_in_date=" . urlencode($checkInDate);
    }
    if ($checkOutDate) {
        $redirectUrl .= "&check_out_date=" . urlencode($checkOutDate);
    }
    
    // Redirect back to room.php with dates
    header('Location: ' . $redirectUrl);
    exit;
} else {
    // Redirect to home page if not a POST request
    header('Location: ../assets/index.php');
    exit;
}