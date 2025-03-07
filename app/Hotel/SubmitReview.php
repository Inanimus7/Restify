<?php
require '/../boot/boot.php';

use Hotel\Review;
use Hotel\User;

header('Content-Type: application/json');

// Ensure user is logged in
$user = new User();
$currentUserId = User::getCurrentUserId();

if (!$currentUserId) {
    echo json_encode(['success' => false, 'message' => 'Please log in to submit a review']);
    exit;
}

// Get POST data
$roomId = $_POST['room_id'] ?? null;
$rating = $_POST['rating'] ?? null;

// Validate inputs
if (!$roomId || !$rating) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Initialize review service
$reviewService = new Review();

// Check if user has already reviewed this room
if ($reviewService->hasUserReviewedRoom($roomId, $currentUserId)) {
    echo json_encode([
        'success' => false, 
        'message' => 'You have already reviewed this room'
    ]);
    exit;
}

// Submit review
$result = $reviewService->submitReview($roomId, $currentUserId, $rating);

echo json_encode([
    'success' => $result, 
    'message' => $result ? 'Review submitted successfully' : 'Failed to submit review'
]);
exit;