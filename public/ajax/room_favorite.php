<?php
// Load required files and initialize Favorite service
require 'C:/xampp/htdocs/vhosts/hotel.collegelink.localhost/Project/boot/boot.php';

// Enable error reporting for debugging 
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

    $isFavorite = $_REQUEST['is_favorite'];
    if (!$isFavorite){
        $status = $favorite->addFavorite($roomId, User::getCurrentUserId());
    } else {
        $status = $favorite->removeFavorite($roomId, User::getCurrentUserid());
    }

    // Return operation status
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'is_favorite' => !$isFavorite
    ]);
}
