<?php
    // Load required files and initialize Room service
    require __DIR__ . '/../../boot/boot.php';

    // Disable the display of warnings
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);

    use Hotel\Room;
    use Hotel\RoomType;
    use Hotel\User;
    use Hotel\Review;

    // Initialize User object
    $user = new User();
    $userName = 'Guest';
    
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Restify</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="styling/list.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="pages/search.js"></script>
</head>
<body>
    <header>
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
        <main class="results" id="search-results-container">
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
                                <button id="booking-btn" onclick="window.location.href='room.php?room_id=<?php echo $availableRoom['room_id']; ?>&check_in_date=<?php echo urlencode($checkInDate ? $checkInDate->format('d/m/Y') : ''); ?>&check_out_date=<?php echo urlencode($checkOutDate ? $checkOutDate->format('d/m/Y') : ''); ?>'"> Start Your Reservation</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h7>We were unable to find any rooms matching your search criteria.</h7>
            <?php endif; ?>
        </main>
        <!-- Sidebar with search criteria and filter options -->
        <aside class="current-search-criteria">
            <div class="back-button-container" title = "Go back">
                <button onclick="window.location.href='index.php'" class="back-button">
                    <i class="fa-sharp fa-solid fa-arrow-left"></i>
                </button>
            </div>
            <h1>Your search</h1>
            <div class="selected-criteria">
                <div class="selected-destination">
                    <h1>Destination</h1>
                    <div class="destination"><i class="fa-solid fa-location-dot"></i><?php echo $city ?: 'Not selected'; ?></div>
                </div>
                <div class="selected-check-in">
                    <h1>Check-in</h1>
                    <div class="check-in-date"><i class="fas fa-calendar-check"></i><?php echo $checkInDate->format('d/m/Y'); ?></div>
                </div>
                <div class="selected-check-out">
                    <h1>Check-out</h1>
                    <div class="check-out-date"><i class="fas fa-calendar-times"></i><?php echo $checkOutDate->format('d/m/Y'); ?></div>
                </div>
                <div class="selected-room-type">
                    <h1>Room Type</h1>
                    <div class="room-type">
                        <i class="fa-solid fa-bed"></i>
                        <?php
                            // Display the selected room type based on $typeId (if no rooms are found)
                            if ($typeId == 1) {
                                echo "Single Room";
                            } elseif ($typeId == 2) {
                                echo "Double Room";
                            } elseif ($typeId == 3) {
                                echo "Triple Room";
                            } elseif ($typeId == 4) {
                                echo "Fourfold Room";
                            } else {
                                echo 'Not selected';
                            }
                        ?>
                    </div>
                </div>
                <div class="selected-price-range">
                    <h1>Price Range</h1>
                    <div class="price-range">
                        <i class="fa-solid fa-euro-sign"></i>
                        <span id="priceRangeDisplay">
                            <?php 
                                $minPrice = isset($_REQUEST['minPrice']) ? $_REQUEST['minPrice'] : 0;
                                $maxPrice = isset($_REQUEST['maxPrice']) ? $_REQUEST['maxPrice'] : 500;
                                echo "$minPrice - $maxPrice";
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filter Section: Search Again Form -->
            <div class="filter-section">
                <h3>Search again</h3>
                <form class="filter-form" method = "GET" autocomplete = "off">
                    <div class="booking-form">
                        <div class="flex-direction">
                            <!-- Destination Input -->
                            <label for="destination">Destination</label>
                            <select id="city" name="city">
                                <option value="" hidden>Where are you going?</option>
                                    <?php
                                        foreach ($cities as $city) {
                                            ?>
                                            <option value = "<?php echo $city?>"><?php echo $city?></option>
                                            <?php
                                        }                    
                                    ?>
                                <option value="">Search all cities</option>    
                            </select>
                        </div>
                        <div class="flex-direction">
                            <label for="check-in-date">Check-In</label>
                            <input type="text" id="check-in-date" name="check_in_date" placeholder="Choose Dates">
                        </div>
                        <div class="flex-direction">
                            <label for="check-out-date">Check-Out</label>
                            <input type="text" id="check-out-date" name="check_out_date" placeholder="Choose Dates">
                        </div>
                        <div class="flex-direction">
                            <label for="guests">Room Type</label>
                            <select id="room_type" name="room_type">
                                <option value="" hidden>Select Room Type</option>
                                <?php
                                        foreach ($allTypes as $RoomType) {
                                            ?>
                                            <option value = "<?php echo $RoomType['type_id']?>"><?php echo $RoomType['title']?></option>
                                            <?php
                                        }                    
                                    ?>
                                <option value="">Search all Room Types</option>
                            </select>
                        </div>
                        <div class="flex-direction">
                            <div class="custom-wrapper">
                                <div class="header">
                                    <h2 class="pricetitle">Price Range</h2>
                                </div>
                                <div class="price-input-container">
                                    <div class="price-input">
                                        <div class="price-field">
                                            <span>Minimum Price: €</span>
                                            <input type="number" class="min-input" name="minPrice" value="<?php echo isset($_REQUEST['minPrice']) ? $_REQUEST['minPrice'] : 0; ?>">
                                        </div>
                                        <div class="price-field">
                                            <span>Maximum Price: €</span>
                                            <input type="number" class="max-input" name="maxPrice" value="<?php echo isset($_REQUEST['maxPrice']) ? $_REQUEST['maxPrice'] : 500; ?>">
                                        </div>
                                    </div>
                                    <div class="slider">
                                        <div class="price-slider"></div>
                                    </div>
                                </div>
                                <!-- Slider -->
                                <div class="range-input">
                                    <input type="range" class="min-range" min="0" max="500" value="0" step="10">
                                    <input type="range" class="max-range" min="0" max="500" value="500" step="10">
                                </div>
                            </div>
                        </div>
                        <div class="flex-direction">
                            <button type="submit" id="search-btn"><i class="fas fa-search"></i>Search</button>
                            <div id="date-error-message" style="display: none;">
                        <p><i class="fas fa-exclamation-triangle"></i> Please provide valid check-in and check-out dates</p>
                    </div>
                        </div>
                    </div>
                </form>
            </div>
        </aside>
    </div> 
    <!-- End of wrapper div -->

    <!-- Footer Section -->
    <footer>
        <p>&copy; Copyright Inanimus Web Development 2024</p>
    </footer>

    <!-- External JavaScript -->
    <script src="list.js"></script>
</body>
</html>
