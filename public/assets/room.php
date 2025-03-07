<?php
    // Load required files and initialize Room service
    require 'C:/xampp/htdocs/vhosts/hotel.collegelink.localhost/Project/boot/boot.php';

    // Disable the display of warnings
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);

    use Hotel\Room;
    use Hotel\RoomType;
    use Hotel\User;

    // Initialize User object
    $user = new User();
    $userName = 'Guest'; // Default if no user is logged in
    
    // Check and verify the user token from the cookie
    if (isset($_COOKIE['user_token'])) {
        $token = $_COOKIE['user_token'];
        $currentUserId = $user->getUserFromToken($token);
        if ($currentUserId) {
            User::setCurrentUserId($currentUserId); // Set static user ID for consistency
            $userName = $user->getUserNameById($currentUserId); // Fetch user's name
        }
    }
    
    // Initialize Room service
    $room = new Room();
    $type = new RoomType();

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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Restify - <?php echo htmlspecialchars($roomData['hotel_name']); ?></title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/fontawesome.min.css">
        <link rel="stylesheet" href="room.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
                    <a href="logout.php" title="Log Out"><i class="fa-solid fa-power-off"></i></a>
                </div>
            </div>
        </header>
        <div class="wrapper">
            <main class="main-content">
                <div class="room-preview">
                    <div class="room-header">
                        <h1><?php echo htmlspecialchars($roomData['hotel_name']); ?></h1>
                        <div class="room-rating">
                            <?php 
                                // Display the rating if available
                                echo isset($roomData['avg_reviews']) ? number_format($roomData['avg_reviews'], 1) . ' / 5.0' : 'No ratings yet'; 
                            ?>
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
                                    <div class="room-location">
                                        <h3>Location: <?php echo htmlspecialchars($roomData['city']) . ', ' . htmlspecialchars($roomData['address']); ?></h3>
                                    </div>
                                    <div class="room-capacity">
                                        <h3>Capacity: <?php echo htmlspecialchars($roomData['count_of_guests']); ?> guests</h3>
                                    </div>
                                    <div class="room-price">
                                        <h3>Price: <?php echo htmlspecialchars($roomData['price_per_night']); ?>€ per night</h3>
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
                            </div> <!-- Closing under-dscrpt -->
                            <div class="book-now-container">
                                <a href="booking.php?room_id=<?php echo $roomId; ?>" class="book-now-btn">Book Now</a>
                            </div>
                        </div> <!-- Closing room-properties -->
                </aside>
            </main>
            <div class = "lower-section">
                <form class="user-reviews" id="review-form" method="post" action="SubmitReview.php">                    
                    <div class="form-group">
                        <div class="star-rating">
                            <h2>Rate the Room</h2>
                            <div class="stars">
                                <span class="star" data-index="0">&#9733;</span>
                                <span class="star" data-index="1">&#9733;</span>
                                <span class="star" data-index="2">&#9733;</span>
                                <span class="star" data-index="3">&#9733;</span>
                                <span class="star" data-index="4">&#9733;</span>
                            </div>
                            <input type="hidden" name="rating" id="rating-value" value="0">
                            <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">
                        </div>
                    </div>
                    <div class = "submit">
                        <button type="submit" class="submit-btn">Submit Review</button>
                    </div>
                    <div id="success-message" style="display: none; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-top: 15px;">
                        Your rating has been successfully submitted! We appreciate your feedback!<i class="fa-solid fa-check"></i>
                    </div>
                </form>
                <div class = "room-location">
                </div>                
            </div>
        <footer>
            <p>&copy; Copyright Inanimus Web Development 2024</p>
        </footer>
        <script src="room.js"></script>        
    </body>    
</html>