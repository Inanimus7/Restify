<?php
    // Load required files and initialize Room service
    require __DIR__ . '/../../boot/boot.php';
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);

    use Hotel\Room;
    use Hotel\RoomType;
    use Hotel\User;
    use Hotel\Favorite;
    use Hotel\Review;
    use Hotel\Booking;
    
    // Initialize User object
    $user = new User();
    $userName = 'Guest'; 
    $currentUserId = null; 

    // Check and verify the user token from the cookie
    if (isset($_COOKIE['user_token'])) {
        $token = $_COOKIE['user_token'];
        $currentUserId = $user->getUserFromToken($token);
        if ($currentUserId) {
            User::setCurrentUserId($currentUserId);
            $userName = $user->getUserNameById($currentUserId);
        }
    }
    // Initialize Room service
    $room = new Room();
    $type = new RoomType();
    $favorite = new Favorite();

    // Get room ID from URL parameter
    $roomId = isset($_GET['room_id']) ? $_GET['room_id'] : null;
    
    // If no room ID is provided, redirect to the listing page
    if (!$roomId) {
        header('Location: list.php');
        exit;
    }
    // Get room data from the database
    $roomData = $room->getRoomById($roomId);
        
    // If room not found, handle the error
    if (!$roomData) {
        header('Location: list.php');
        exit;
    }
    //Check if room is favorite for current user
    $isFavorite = $favorite->isFavorite($roomId, $currentUserId);

    //Load all reviews
    $review = new Review();
    $allReviews = $review->getReviewsByRoom($roomId);

    // Get check-in and check-out dates from request
    $checkInDate = isset($_REQUEST['check_in_date']) ? 
        DateTime::createFromFormat('d/m/Y', $_REQUEST['check_in_date']) : null;
    $checkOutDate = isset($_REQUEST['check_out_date']) ? 
        DateTime::createFromFormat('d/m/Y', $_REQUEST['check_out_date']) : null;

    // Initialize $alreadyBooked as false
    $alreadyBooked = false;

    // Only check for bookings if we have valid dates
    if ($checkInDate && $checkOutDate) {
        // Format dates for database query
        $checkInDateForDb = $checkInDate->format('Y-m-d');
        $checkOutDateForDb = $checkOutDate->format('Y-m-d');
        
        // Look for bookings
        $booking = new Booking();
        $alreadyBooked = $booking->isBooked($roomId, $checkInDateForDb, $checkOutDateForDb);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Restify - <?php echo htmlspecialchars($roomData['hotel_name']); ?></title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="styling/room.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
    </head>
    <body>
        <header>
            <!-- Left Content: Restify title and navigation menu -->
            <div class="left-content">
                <div class="restify-title">
                    <a href="index.php">Restify.</a>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#services">Hotels</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Right Content: Profile and Logout -->
            <div class="auth-buttons">
                <div class="profile-btn">
                    <a href="profile.php" title="View Profile">
                        <i class="fas fa-user"></i><?php echo htmlspecialchars($userName); ?>
                    </a>
                </div>
                <div class="logout-btn">
                    <a href="../actions/logout.php" title="Log Out"><i class="fas fa-power-off"></i></a>
                </div>
            </div>
        </header>
        <div class="wrapper">
            <main class="main-content">
                <div class="room-preview">
                    <div class="room-header">
                        <h1><?php echo htmlspecialchars($roomData['hotel_name']); ?></h1>
                        <div class="header-right">
                            <div class="add-favorites">
                                <div class="add-favorites">
                                    <form name="favoriteForm" method="POST" id="favoriteForm" class="favoriteForm" action="../actions/favorite.php">
                                        <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $currentUserId; ?>">
                                        <input type="hidden" name="action" value="<?php echo $isFavorite ? 'remove' : 'add'; ?>">
                                        <?php if ($checkInDate && $checkOutDate): ?>
                                            <input type="hidden" name="check_in_date" value="<?php echo $checkInDate->format('d/m/Y'); ?>">
                                            <input type="hidden" name="check_out_date" value="<?php echo $checkOutDate->format('d/m/Y'); ?>">
                                        <?php endif; ?>
                                        <button type="submit" class="add-favorite-btn" title="<?php echo $isFavorite ? 'Remove from Favorites' : 'Add to Favorites'; ?>">
                                            <i class="fa-solid fa-heart <?php echo $isFavorite ? 'favorited' : ''; ?>"></i>
                                        </button>
                                    </form>      
                                </div>      
                            </div>
                            <div class="room-rating">
                                <?php 
                                    $roomAvgReview = floatval($roomData['avg_reviews'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($roomAvgReview >= $i) {
                                            ?>
                                            <span class="fa fa-star checked"></span>
                                            <?php
                                        } else {
                                            ?>
                                            <span class="fa fa-star"></span>
                                            <?php
                                        }
                                    }
                                ?>                  
                            </div>
                        </div>
                    </div>
                    <img src="images/rooms/<?php echo htmlspecialchars($roomData['photo_url']); ?>" alt="<?php echo htmlspecialchars($roomData['hotel_name']); ?>">     
                </div>
                <aside class="room-details">
                    <div class="room-dscrpt">
                        <h1>Room description</h1>
                        <p><?php echo htmlspecialchars($roomData['description_long']); ?></p>
                        <div class="room-properties">
                            <h2>Room Details & Features</h2>
                            <div class="under-dscrpt">
                                <div class="detailing">
                                    <h3>Room Type: <?php echo htmlspecialchars($roomData['room_type_title']); ?></h3>
                                    <div class="room-address">
                                        <h3>Location: <?php echo htmlspecialchars($roomData['city']) . ', ' . htmlspecialchars($roomData['address']); ?></h3>
                                    </div>
                                    <div class="room-capacity">
                                        <h3>Capacity: <?php echo htmlspecialchars($roomData['count_of_guests']); ?> guests</h3>
                                    </div>
                                    <div class="room-price">
                                        <h3>Price: <?php echo htmlspecialchars($roomData['price_per_night']); ?>€ per night</h3>
                                    </div>
                                    <div class="total-price">
                                        <?php 
                                        if ($checkInDate && $checkOutDate) {
                                            $daysDiff = $checkOutDate->diff($checkInDate)->days;
                                            $daysDiff = max(1, $daysDiff);
                                            $totalPrice = $daysDiff * $roomData['price_per_night'];
                                            ?>
                                            <h3>Total Price: <?php echo $totalPrice; ?>€ (<?php echo $daysDiff; ?> night<?php echo $daysDiff > 1 ? 's' : ''; ?>)</h3>
                                        <?php } ?>
                                    </div>
                                </div>
                                <ul>
                                    <?php if ($roomData['wifi']): ?>
                                        <li><i class="fa-solid fa-wifi"></i> Free Wifi</li>
                                    <?php endif; ?>
                                    <?php if ($roomData['parking']): ?>
                                        <li><i class="fa-solid fa-square-parking"></i> Free Parking</li>
                                    <?php endif; ?>
                                    <?php if ($roomData['pet_friendly']): ?>
                                        <li><i class="fa-solid fa-dog"></i> Pet Friendly</li>
                                    <?php endif; ?>
                                </ul>
                            </div> 
                            <?php if ($alreadyBooked || (isset($_GET['booking']) && $_GET['booking'] === 'success')): ?>
                                <?php if (isset($_GET['booking']) && $_GET['booking'] === 'success'): ?>
                                    <div id="booking-success-message" style="padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-top: 15px;">
                                        Your booking has been successfully completed! Enjoy your stay! <i class="fa-solid fa-check"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="booked-message" <?php echo (isset($_GET['booking']) && $_GET['booking'] === 'success') ? 'style="display: none;"' : ''; ?>>
                                    <span><p>Already Booked</p></span>
                                </div>
                            <?php else: ?>
                                <form class="bookingForm" name="bookingForm" method="POST" action="../actions/book.php">  
                                    <div class="book-now-container">
                                        <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">
                                        <?php if ($checkInDate && $checkOutDate): ?>
                                            <input type="hidden" name="check_in_date" value="<?php echo $checkInDateForDb; ?>">
                                            <input type="hidden" name="check_out_date" value="<?php echo $checkOutDateForDb; ?>">
                                        <?php endif; ?>
                                        <button type="submit" class="book-now-btn">Book Now</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div> 
                    </div> 
                </aside>
            </main>
            <div class = "lower-section">
                <div class = "whole-reviews">
                <div class="reviews-section">
                    <h3>Reviews</h3>
                    <?php
                    if (empty($allReviews)) {
                        // Display message when there are no reviews
                        ?>
                        <div class="no-reviews-message">
                            <p>There are no reviews for this room yet. Be the first to review this room!</p>
                        </div>
                        <?php
                    } else {
                        // Display reviews if they exist
                        foreach ($allReviews as $counter => $review) {
                            ?>
                            <div class="reviews-posted">
                                <div class="reviews-posted-header">
                                    <h4>
                                        <span><?php echo sprintf('%d. %s', $counter + 1, $review['user_name']); ?></span>
                                        <div class="star-div-posted">
                                            <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($review['rate'] >= $i) {
                                                    ?>
                                                    <span class="fa fa-star checked"></span>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span class="fa fa-star"></span>
                                                    <?php
                                                }
                                            }
                                            ?>  
                                        </div>
                                    </h4>
                                </div>
                                <p><?php echo $review['comment']; ?></p>
                                <h5>Created at: <?php echo $review['created_time']; ?></h5>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <form class="user-reviews" id="review-form" method="post" action="../actions/review.php">                    
                    <div class="form-group">
                    <div class="star-rating">
                        <div class="star-rating-header">
                            <h2>Add Your Review</h2>
                            <input type="hidden" name="room_id" value="<?php echo $roomId ?>">
                            <div class="rating">
                                <input type="radio" id="star5" name="rate" value="5" />
                                <label for="star5" title="Awesome - 5 stars"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star4" name="rate" value="4" />
                                <label for="star4" title="Pretty Good - 4 stars"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star3" name="rate" value="3" />
                                <label for="star3" title="Decent - 3 stars"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star2" name="rate" value="2" />
                                <label for="star2" title="Not that good - 2 stars"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star1" name="rate" value="1" />
                                <label for="star1" title="Terrible - 1 star"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>
                        <div class="comment-area">
                            <textarea name="comment" id="reviewField" rows="4" cols="50" placeholder="Share your experience..."></textarea>
                        </div>
                    </div>
                    <div class = "submit">
                        <button type="submit" class="submit-btn">Submit Review</button>
                    </div>
                    <div id="review-success-message" style="display: none; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-top: 15px;">
                        Your rating has been successfully submitted! We appreciate your feedback! <i class="fa-solid fa-check"></i>
                    </div>
                </form>
                </div>
                </div>
                <!-- Doesnt work at the moment/neither tested due to payment issues -->
                <div class="room-location" 
                    data-lat="<?php echo htmlspecialchars($roomData['latitude'] ?? ''); ?>" 
                    data-lng="<?php echo htmlspecialchars($roomData['longitude'] ?? ''); ?>"
                    data-hotel-name="<?php echo htmlspecialchars($roomData['hotel_name'] ?? ''); ?>"
                    data-address="<?php echo htmlspecialchars($roomData['address'] . ', ' . $roomData['city'] ?? ''); ?>">
                    <div class="map-error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                    <p>We’re sorry, but Google Maps is currently unavailable. Our team is working diligently to resolve this issue.</p>
                </div>
                </div>             
            </div>
        <footer>
            <p>&copy; Copyright Inanimus Web Development 2024</p>
        </footer>
        <script src="room.js"></script>        
    </body>    
</html>