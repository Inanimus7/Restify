<?php

use Hotel\User;
use Hotel\Review;

// Include bootstrap file
require_once __DIR__ .'/../../boot/boot.php';

// Check if request method is POST
if(strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
    header('Location: /Project/public/assets/login.php');
    return;
}

//If no user is logged in, return to main page
if (empty(User::getCurrentUserId())) {
    header('Location: /');
    return;
}

//Check if room id is given
$roomId = $_REQUEST['room_id'];
if (empty($roomId)) {
    header('Location: /');
    return;
}

//Add review
$review = new Review();
$success = $review->addReview($roomId, User::getCurrentUserId(), $_REQUEST['rate'], $_REQUEST['comment']);

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    // AJAX response
    //Get all reviews
    $roomReviews = $review->getReviewsByRoom($roomId);
    $counter = count($roomReviews);

    //Load User
    $user = new User();
    $userInfo = $user->getUserById(User::getCurrentUserId());

    $reviewHtml = '
    <div class="reviews-posted">
        <div class="reviews-posted-header">
            <h4>
                <span>'.sprintf('%d. %s', $counter, $userInfo['name']).'</span>
                <div class="star-div-posted">';

    // Generate star rating HTML
    for ($i = 1; $i <= 5; $i++) {
        $reviewHtml .= $_REQUEST['rate'] >= $i ? 
            '<span class="fa fa-star checked"></span>' : 
            '<span class="fa fa-star"></span>';
    }

    $reviewHtml .= '
                </div>
            </h4>
        </div>
        <p>'.htmlspecialchars($_REQUEST['comment']).'</p>
        <h5>Created at: '.(new DateTime())->format('Y-m-d H:i:s').'</h5>
    </div>';

    // Set JSON header
    header('Content-Type: application/json');

    // Return JSON response
    echo json_encode([
        'status' => 'success',
        'message' => 'Review added successfully',
        'review_html' => $reviewHtml,
        'review_count' => $counter
    ]);
    exit;
} else {
    // Redirect for non-AJAX requests
    header('Location: /Project/public/assets/room.php?room_id=' . $roomId);
    exit;
}