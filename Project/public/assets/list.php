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
    $allTypes = $type->getAllTypes();

    // Get all cities for the search dropdown
    $cities = $room->getCities();

    // Get page parameters from the request
    // Get page parameters from the request with proper null/empty handling
    $city = isset($_REQUEST['city']) && $_REQUEST['city'] != 'null' ? $_REQUEST['city'] : null;
    $typeId = isset($_REQUEST['room_type']) && $_REQUEST['room_type'] != 'null' ? $_REQUEST['room_type'] : null;
    $checkInDate = DateTime::createFromFormat('d/m/Y', $_REQUEST['check_in_date']);
    $checkOutDate = DateTime::createFromFormat('d/m/Y', $_REQUEST['check_out_date']);
    $minPrice = isset($_REQUEST['minPrice']) ? $_REQUEST['minPrice'] : null;
    $maxPrice = isset($_REQUEST['maxPrice']) ? $_REQUEST['maxPrice'] : null;

    // Format dates for database query
    $checkInDateForDb = $checkInDate->format('Y-m-d');
    $checkOutDateForDb = $checkOutDate->format('Y-m-d');

            // Check if the dates are valid before proceeding
        if (!$checkInDate || !$checkOutDate) {
            // Handle the error gracefully
            $checkInDate = $checkOutDate = null;
        }

        // Format dates for database query only if they are valid
        if ($checkInDate && $checkOutDate) {
            $checkInDateForDb = $checkInDate->format('Y-m-d');
            $checkOutDateForDb = $checkOutDate->format('Y-m-d');
        } else {
            // If the dates are invalid, display a message or handle the error
            $checkInDateForDb = $checkOutDateForDb = null;
        }
    // Perform search and retrieve results and total count
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

    <!-- External Stylesheets -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="list.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- External Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    <!-- Header Section -->
    <header>
        <!-- Left Content: Restify title and navigation menu -->
        <div class="left-content">
            <div class="restify-title">
                <a href = "index.php">Restify.</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#services">Hotels</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>

        <!-- Right Content: Sign In and Sign Up buttons -->
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

    <!-- Main Content -->
    <div class="wrapper">
        <main class="results">
            <!-- Display the number of results based on the search criteria -->
            <div class="number-of-results">
             <?php
            // Check if the dates are valid before displaying the result message
            if ($checkInDate && $checkOutDate) {
                if ($totalRoomsFound > 0) {
                    echo $totalRoomsFound . " room" . ($totalRoomsFound > 1 ? "s" : "") . " found matching your criteria.";
                } else {
                    echo "No rooms found matching your criteria.";
                }
            } else {
                // Display a message if the dates are invalid
                echo "Please provide valid check-in and check-out dates.";
            }
            ?>
        </div>
            <h1>Search Results</h1>

            <!-- Display available rooms -->
            <?php if ($totalRoomsFound > 0): ?>
                <?php foreach ($allAvailableRooms as $availableRoom): ?>
                    <div class="results-template">
                        <div class="left-side">
                            <div class="room-img">
                            <div class="results-img" style="background-image: url('images/rooms/<?php echo $availableRoom['photo_url']; ?>');"></div>
                            </div>
                            <div class="room-details">
                                <h1>
                                <a href="room.php?room_id=<?php echo $availableRoom['room_id']; ?>">
                                    <?php echo $availableRoom['hotel_name']; ?>
                                </a>
                                </h1>
                                <h2><?php echo $availableRoom['city'] . ', ' . $availableRoom['address']; ?></h2> <!-- City and address with a comma between them -->
                                <p><?php echo $availableRoom['description_short']; ?></p> <!-- Short description -->
                                <h3>Room Type: <?php echo $availableRoom['room_type_title']; ?></h3> <!-- Display room type title -->
                            </div>
                        </div>
                        <div class="right-side">
                            <div class="room-rating">Room Rating</div>
                            <div class="price-per-night"><?php echo $availableRoom['price_per_night']; ?>€ per night</div>
                            <h1>Number of Nights: <?php echo $checkOutDate->diff($checkInDate)->days; ?> Number of guests: <?php echo $availableRoom['count_of_guests']; ?></h1>
                            <div>
                            <button id="booking-btn" onclick="window.location.href='room.php?room_id=<?php echo $availableRoom['room_id']; ?>'">See Booking Options</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Display message when no rooms found -->
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
                        <span id="priceRangeDisplay"></span>
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
                            <label for="price-range">Price Range</label>
                            <!-- First range input for minimum value -->
                            <input type="range" id="minPriceRange" min="170" max="500" step="10" value="<?php echo isset($_REQUEST['minPrice']) ? $_REQUEST['minPrice'] : 170; ?>">
                            <input type="text" id="minRangeInput" value="170 €" readonly>
                            <!-- Second range input for maximum value -->
                            <input type="range" id="maxPriceRange" min="170" max="500" step="10" value="<?php echo isset($_REQUEST['maxPrice']) ? $_REQUEST['maxPrice'] : 500; ?>">
                            <input type="text" id="maxRangeInput" value="500 €" readonly>
                            <!-- Hidden inputs will be created by JavaScript -->
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
