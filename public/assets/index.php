<?php
require __DIR__ . '/../../boot/boot.php';

// Disable the display of warnings
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

use Hotel\Room;
use Hotel\RoomType;
use Hotel\User;


// Check for user authentication
$userToken = $_COOKIE['user_token'] ?? '';
$user = new User();

if (empty($userToken) || !$user->verifyToken($userToken)) {
    // Not authenticated, redirect to login
    header('Location: /Project/public/assets/login.php');
    exit;
}

// Get user ID and set current user
$userId = $user->getUserFromToken($userToken);
User::setCurrentUserId($userId);

// Get user's name
$userName = $user->getUserNameById($userId);

// Get cities
$room = new Room();
$cities = $room->getCities();

// Get all room types
$type = new RoomType();
$allTypes = $type->getAllTypes();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Restify</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/fontawesome.min.css">
        <link rel="stylesheet" href="styling/index.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    </head>
    <body>
        <div class="img-background"></div>
            <header>
         <!-- Left Content: Restify title and navigation menu -->
                <div class="left-content">
                    <div class="restify-title">
                        <h1>Restify.</h1>
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
                    <a href="../actions/logout.php" title="Log Out"><i class="fas fa-power-off"></i></a>
                </div>
                </div>
            </header>
            <div class="content">
                <div class="hero-section">
                    <h1>Get Started to Book</h1>
                    <h2>Your Perfect Escape</h2>
                    <p>Restify makes booking your dream getaway easy.</p> 
                    <p>Discover top hotels and destinations with just a few clicks.</p>
                </div>
                <form action="list.php" method="GET" autocomplete="off" class="filter-form">
                 <div class="booking-form">
                        <div>
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
                        <div>
                            <label for="check-in-date">Check-In</label>
                            <input type="text" id="check-in-date" name="check_in_date" placeholder="Choose Dates">
                        </div>
                        <div>
                            <label for="check-out-date">Check-Out</label>
                            <input type="text" id="check-out-date" name="check_out_date" placeholder="Choose Dates">
                        </div>
                        <div>
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
                        <div>
                            <!-- Search Button -->
                            <button type="submit" id="search-btn"><i class="fas fa-search"></i>Search</button>
                        </div>
                    </div>
                    <div id="date-error-message" style="display: none;">
                        <p><i class="fas fa-exclamation-triangle"></i> Please provide valid check-in and check-out dates</p>
                    </div>
                  </form>                
            </div>
            <footer>
                <p>&copy; Copyright Inanimus Web Development 2024</p>
             </footer>
             <script src="index.js"></script>
             <script>
                         document.querySelector('form').addEventListener('submit', function() {
                         console.log('Form submitted');
                         });
            </script>        
    </body>    
</html>