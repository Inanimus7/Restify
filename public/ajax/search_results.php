<?php
    // Load required files and initialize Room service
    require 'C:/xampp/htdocs/vhosts/hotel.collegelink.localhost/Project/boot/boot.php';

    // Disable the display of warnings
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);

    use Hotel\Room;
    use Hotel\RoomType;
    use Hotel\User;
    use Hotel\Review;

    // Initialize User object
    $user = new User();
    $userName = 'Guest'; // Default if no user is logged in
    
    // Check and verify the user token from the cookie
    if (isset($_COOKIE['user_token'])) {
        $token = $_COOKIE['user_token'];
        $currentUserId = $user->getUserFromToken($token);
        if ($currentUserId) {
            User::setCurrentUserId($currentUserId);
            $userName = $user->getUserNameById($currentUserId);
        }
    }
    
    // Initialize Room and Review services
    $room = new Room();
    $type = new RoomType();
    $review = new Review();
    $allTypes = $type->getAllTypes();

    // Get all cities for the search dropdown
    $cities = $room->getCities();

    // Get page parameters from the request
    $city = isset($_REQUEST['city']) && $_REQUEST['city'] != 'null' ? $_REQUEST['city'] : null;
    $typeId = isset($_REQUEST['room_type']) && $_REQUEST['room_type'] != 'null' ? $_REQUEST['room_type'] : null;
    $checkInDate = DateTime::createFromFormat('d/m/Y', $_REQUEST['check_in_date']);
    $checkOutDate = DateTime::createFromFormat('d/m/Y', $_REQUEST['check_out_date']);
    $minPrice = isset($_REQUEST['minPrice']) ? $_REQUEST['minPrice'] : null;
    $maxPrice = isset($_REQUEST['maxPrice']) ? $_REQUEST['maxPrice'] : null;

    // Check and format dates
    if ($checkInDate && $checkOutDate) {
        $checkInDateForDb = $checkInDate->format('Y-m-d');
        $checkOutDateForDb = $checkOutDate->format('Y-m-d');
    } else {
        $checkInDate = $checkOutDate = null;
        $checkInDateForDb = $checkOutDateForDb = null;
    }

    // Perform search and retrieve results
    $searchResult = $room->search($checkInDate, $checkOutDate, $city, $typeId, $minPrice, $maxPrice);
    $allAvailableRooms = $searchResult['results'];
    $totalRoomsFound = $searchResult['count'];
?>
<div class="number-of-results">
    <?php
    if ($checkInDate && $checkOutDate) {
        if ($totalRoomsFound > 0) {
            echo $totalRoomsFound . " room" . ($totalRoomsFound > 1 ? "s" : "") . " found matching your criteria.";
        } else {
            echo "No rooms found matching your criteria.";
        }
    } else {
        echo "Please provide valid check-in and check-out dates.";
    }
    ?>
</div>
<h1>Search Results</h1>

<?php if ($totalRoomsFound > 0): ?>
    <?php foreach ($allAvailableRooms as $availableRoom): ?>
        <div class="results-template">
            <div class="left-side">
                <div class="room-img">
                    <div class="results-img" style="background-image: url('images/rooms/<?php echo $availableRoom['photo_url']; ?>');"></div>
                </div>
                <div class="room-details">
                    <h1>
                        <a href="room.php?room_id=<?php echo $availableRoom['room_id']; ?>&check_in_date=<?php echo urlencode($checkInDate ? $checkInDate->format('d/m/Y') : ''); ?>&check_out_date=<?php echo urlencode($checkOutDate ? $checkOutDate->format('d/m/Y') : ''); ?>">
                            <?php echo $availableRoom['hotel_name']; ?>
                        </a>
                    </h1>
                    <h2><?php echo $availableRoom['city'] . ', ' . $availableRoom['address']; ?></h2>
                    <p><?php echo $availableRoom['description_short']; ?></p>
                    <h3>Room Type: <?php echo $availableRoom['room_type_title']; ?></h3>
                </div>
            </div>
            <div class="right-side">
                <?php
                // Fetch average rating for this room
                $reviews = $review->getReviewsByRoom($availableRoom['room_id']);
                $avgRating = 0;
                if (!empty($reviews)) {
                    $totalStars = array_sum(array_column($reviews, 'rate'));
                    $avgRating = round($totalStars / count($reviews), 1);
                }
                ?>
                <div class="room-rating">
                    <?php if ($avgRating > 0): ?>
                        <?php echo number_format($avgRating, 1); ?> <i class="fa fa-star" style="color: #ffd700;"></i>
                    <?php else: ?>
                        <div class = "no-room-rating">
                            <p> (No Room Rating)</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="price-per-night"><?php echo $availableRoom['price_per_night']; ?>€ per night</div>
                <div class="number-of-container">
                    <div class="number-of-nights">
                        <h1>Number of Nights: <?php echo $checkOutDate && $checkInDate ? $checkOutDate->diff($checkInDate)->days : 'N/A'; ?></h1>
                    </div>
                    <div class="number-of-guests">
                        <h1>Number of guests: <?php echo $availableRoom['count_of_guests']; ?></h1>
                    </div>
                </div>
                <div>
                    <button id="booking-btn" onclick="window.location.href='room.php?room_id=<?php echo $availableRoom['room_id']; ?>&check_in_date=<?php echo urlencode($checkInDate ? $checkInDate->format('d/m/Y') : ''); ?>&check_out_date=<?php echo urlencode($checkOutDate ? $checkOutDate->format('d/m/Y') : ''); ?>'">Book Now</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <h7>We were unable to find any rooms matching your search criteria.</h7>
<?php endif; ?>