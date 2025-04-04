<?php
// Load required files and initialize Room service
require __DIR__ . '/../../boot/boot.php';
// Disable the display of warnings
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
$userName = 'Guest'; // Default if no user is logged in
$currentUserId = null; // Default value if no user is logged in

// Check and verify the user token from the cookie
if (isset($_COOKIE['user_token'])) {
    $token = $_COOKIE['user_token'];
    $currentUserId = $user->getUserFromToken($token);
    if ($currentUserId) {
        User::setCurrentUserId($currentUserId);
        $userName = $user->getUserNameById($currentUserId);
    }
}

// Get user details for the personal info section
$userDetails = null;
$userInfoMessage = '';
if ($currentUserId) {
    try {
        // Use our new public method instead of the protected fetch method
        $userDetails = $user->getUserById($currentUserId);
        
        // If no user found, set a message
        if (!$userDetails) {
            $userInfoMessage = "Unable to find user details.";
        }
    } catch (Exception $e) {
        $userInfoMessage = "Error fetching user details: " . $e->getMessage();
    }
}

// Handle password change if form is submitted
$passwordChangeMessage = '';
$passwordChangeSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Verify current password
    if ($userDetails && $user->verify($userDetails['email'], $currentPassword)) {
        // Check if new passwords match
        if ($newPassword === $confirmPassword) {
            try {
                // Update password in database - we need a public method for this too
                $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
                
                // Add a public method in User class for password change
                $result = $user->updatePassword($currentUserId, $passwordHash);
                
                $passwordChangeSuccess = true;
                $passwordChangeMessage = 'Password changed successfully.';
            } catch (Exception $e) {
                $passwordChangeMessage = 'Failed to update password: ' . $e->getMessage();
            }
        } else {
            $passwordChangeMessage = 'New passwords do not match.';
        }
    } else {
        $passwordChangeMessage = 'Current password is incorrect.';
    }
}

// Get all favorites
$favorite = new Favorite();
$userFavorites = $favorite->getListByUser(User::getCurrentUserId());

// Get all Reviews
$review = new Review();
$userReviews = $review->getListByUser(User::getCurrentUserId());

// Get all Bookings
$booking = new Booking();
$userBookings = $booking->getListByUser(User::getCurrentUserId());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restify</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="styling/profile.css">
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
        <div class="wrapper">
        <aside class="profile-toggle">
            <div class="upper-aside-section">
                <div class="user-profile">
                    <img id="profile-image" src="" alt="User Image" />
                    <i id="fallback-icon" class="fas fa-user"></i>
                </div>
                <div class="file-upload-container">
                    <input type="file" id="file-upload" class="file-input" accept="image/*" />
                    <label for="file-upload" class="file-label">
                        <i class="fa-solid fa-camera"></i> Upload your Photo
                    </label>
                </div>
            </div>
            <div class="user-options">
                <ul>
                    <li data-target="personal-info"><i class="fa-solid fa-user"></i>Personal Info</li>
                    <li data-target="booking-history"><i class="fa-solid fa-clock-rotate-left"></i>Your Bookings</li>
                    <li data-target="user-favorites"><i class="fa-solid fa-heart"></i>Your Favorites</li>
                    <li data-target="user-reviews"><i class="fa-solid fa-comment"></i>Your Reviews</li>
                </ul>
            </div>
        </aside>
        <main class="main-content">
            <div id="personal-info" class="content-section">
                    <div class="personal-adj">
                        <?php if ($currentUserId && $userDetails): ?>
                            <div class="grid-template">
                                <?php
                                    // Split the name into first and last name
                                    $nameParts = explode(' ', $userDetails['name'], 2);
                                    $firstName = $nameParts[0];
                                    $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                                ?>
                                <div class="user-info-temp">                        
                                <h2><span>Personal information</span></h2>
                                    <div class="user-titles">
                                        <p class="first-name">First Name: <span id="user-name"><?php echo htmlspecialchars($firstName);?></span></p>
                                        <p class="last-name">Last Name: <span id="user-surname"><?php echo htmlspecialchars($lastName); ?></span></p>
                                        <p class="email-user">E-mail:  <span id="user-email"><?php echo htmlspecialchars($userDetails['email']); ?></span></p>
                                </div>
                            </div>
                            <!-- Password Change Form -->
                                <div class="password-change-section">
                                    <h3>Change Password</h3>
                                    <form method="POST" action="profile.php" class="password-form">
                                        <div class="form-group">
                                            <input 
                                                type="password" 
                                                id="current_password" 
                                                name="current_password" 
                                                placeholder=" " 
                                                required 
                                                class="form-control"
                                            >
                                            <label for="current_password">Current Password</label>
                                            <i class="fa-solid fa-eye-slash toggle-password" title = "Show Password"></i>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input 
                                                type="password" 
                                                id="new_password" 
                                                name="new_password" 
                                                placeholder=" " 
                                                required 
                                                class="form-control"
                                            >
                                            <label for="new_password">New Password</label>
                                            <i class="fa-solid fa-eye-slash toggle-password" title = "Show Password"></i>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input 
                                                type="password" 
                                                id="confirm_password" 
                                                name="confirm_password" 
                                                placeholder=" " 
                                                required 
                                                class="form-control"
                                            >
                                            <label for="confirm_password">Confirm New Password</label>
                                            <i class="fa-solid fa-eye-slash toggle-password" title = "Show Password"></i>
                                        </div>
                                        
                                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                        <div class="password-error d-none">Your new password must contain at least 8 characters.</div>
                                    </form>
                                </div>
                        <?php else: ?>
                            <div class="not-logged-in">
                                <p>Please log in to view your personal information.</p>
                                <?php if (isset($userInfoMessage)): ?>
                                    <p class="error-message"><?php echo htmlspecialchars($userInfoMessage); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($passwordChangeMessage): ?>
                            <div class="alert <?php echo $passwordChangeSuccess ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo htmlspecialchars($passwordChangeMessage); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="booking-history" class="content-section">
                <div class="flex-temp">
                    <?php
                    if (count($userBookings) > 0) {
                        ?>
                        <div class="bookings-container">
                            <?php
                            foreach($userBookings as $booking) {
                                ?>
                                <div class="grid-booking">
                                    <div class="booking-image">
                                        <img src="images/rooms/<?php echo $booking['photo_url']; ?>" />
                                    </div>
                                    <div class="booking-details">
                                        <h1><?php echo $booking['name'];?></h1>
                                        <h2><?php echo sprintf('%s, %s',$booking['city'], $booking['area']);?></h2>
                                        <p><?php echo $booking['description_short'];?></p>
                                    </div>
                                    <div class="booking-actions">
                                        <div class="booking-dates">
                                            <p><span>Check in date: </span> <?php echo $booking['check_in_date'];?></p>
                                            <p><span>Check out date: </span><?php echo $booking['check_out_date'];?></p>
                                        </div>
                                        <div class="booking-price-type">
                                            <p><span>Total Price: </span> <?php echo $booking['total_price'];?>€ </p>
                                            <p><span>Room Type: </span><?php echo $booking['room_type'];?></p>
                                        </div>
                                        <a href="room.php?room_id=<?php echo $booking['room_id']; ?>">Go To Room Page</a>                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="no-booking-message">
                            <p>No bookings found. Ready to plan your next trip?</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div id = "user-favorites" class = "content-section">
                <?php 
                    if (count($userFavorites)> 0) {
                ?>
                <?php
                foreach ($userFavorites as $favorite) {
                    ?>
                <div class = "favorite-template">
                <h3>        
                    <a href="room.php?room_id=<?php echo $favorite['room_id']; ?>"><?php echo htmlspecialchars($favorite['name']); ?></a>
                    <button><a href="room.php?room_id=<?php echo $favorite['room_id']; ?>">Go to Room Page</a></button>
                </h3>
                </div>
                <?php
                }
                ?>
                <?php
                    } else {
                ?>
                <div class="flex-temp">
                <div class = "noFavorite">
                    <p>You haven’t added any favorites yet. Explore and save your top picks!</p>
                </div>
                    </div>
                <?php
                    }
                    ?>
            </div>
            <div id="user-reviews" class="content-section">
            <?php 
                    if (count($userReviews)> 0) {
                ?>
                <?php
                foreach ($userReviews as $review) {
                ?>
                <div class="review-template">
                    <div class="review-template-header">
                    <h2 class="hotel-name">
                        <a href="room.php?room_id=<?php echo $review['room_id']; ?>"><?php echo htmlspecialchars($review['name']); ?></a>
                    </h2>
                        <div class="rating">
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
                    </div>
                    <p><?php echo $review['comment']; ?></p>
                    <h5>Created at: <?php echo $review['created_time']; ?></h5>
                </div>
                <?php
                }
                ?>
                <?php 
                    } else {
                        ?>
                        <div class = "noReview">
                        <p>You haven’t submitted any reviews yet. Share your experiences to help others!<p>
                    </div>
                    <?php 
                    }
                    ?>
            </div>
        </main>
    </div>
    <footer>
        <p>&copy; Copyright Inanimus Web Development 2024</p>
    </footer>
    <script src="profile.js"></script>
</body>
</html>
