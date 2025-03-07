<?php
// Include the application bootstrap file to load dependencies and configurations
require 'C:/xampp/htdocs/vhosts/hotel.collegelink.localhost/Project/boot/boot.php';

// Use the User class from the Hotel namespace for user-related functionality
use Hotel\User;

// Check for error parameters
$error = $_GET['error'] ?? '';
$showError = !empty($error);

// Check if a user is already logged in; redirect to dashboard if true
if (!empty(User::getCurrentUserId())) {
    header('Location: /Project/public/assets/index.php');
    die; // Terminate script execution after redirect
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Meta tags for character encoding and responsive viewport -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <!-- Page title -->
        <title>Restify</title>
        
        <!-- External CSS libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/fontawesome.min.css">
        
        <!-- Custom stylesheet for login page -->
        <link rel="stylesheet" href="login.css">
        
        <!-- jQuery UI CSS for potential UI enhancements -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        
        <!-- JavaScript libraries for interactivity -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    </head>
    
    <body>
        <!-- Background image overlay for visual styling -->
        <div class="img-background"></div>
        
        <!-- Main wrapper for page content -->
        <div class="wrapper">
            <!-- Header section with site branding -->
            <header>
                <div class="restify-title">
                    <a href="index.php">Restify.</a> <!-- Link to homepage -->
                </div>
            </header>
            
            <!-- Main content area -->
            <main>
                <!-- Login form with POST method to submit credentials -->
                <form class="login-form" method="POST" action="../actions/login.php" autocomplete="off">
                    <!-- Form header with title and instruction -->
                    <div class="form-header">
                        <h1><span>Restify.</span></h1> <!-- Styled site name -->
                        <p>Enter your login credentials</p> <!-- User prompt -->
                    </div>
                    
                    <!-- Email input group -->
                    <div class="form-group">
                        <input 
                            id="email" 
                            type="email" 
                            placeholder=" " 
                            name="email" 
                            autocomplete="new-email" 
                        > <!-- Email input with autocomplete prevention -->
                        <label for="email">Enter your E-mail</label> <!-- Floating label -->
                        <i class="fas fa-user"></i> <!-- Icon for visual cue -->
                    </div>
                    
                    <!-- Email validation error message (hidden by default) -->
                    <div class="email-error d-none">
                        <i class="fas fa-exclamation-triangle"></i>Please enter a valid e-mail.
                    </div>
                    
                    <!-- Password input group -->
                    <div class="form-group">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            placeholder=" " 
                            autocomplete="new-password" 
                        > <!-- Password input with autocomplete prevention -->
                        <label for="password">Enter your Password</label> <!-- Floating label -->
                        <i class="fas fa-lock"></i> <!-- Icon for visual cue -->
                    </div>
                    
                    <!-- Checkbox to toggle password visibility -->
                    <div class="checkbox-container">
                        <label for="showPassword">Show Password</label>
                        <input 
                            type="checkbox" 
                            id="showPassword" 
                            onclick="togglePasswordVisibility()"
                        > <!-- Triggers JS function -->
                    </div>
                    
                    <!-- Submit button for form -->
                    <button type="submit" id="login-btn">Login</button>
                    
                    <!-- Password error message (hidden by default, for server-side feedback) -->
                    <div class="password-error <?php echo $showError ? '' : 'd-none'; ?>">
                    <i class="fas fa-exclamation-triangle"></i> Incorrect username or password. Please try again.
                    </div>
                    
                    <!-- General form error message (hidden by default) -->
                    <div class="form-error d-none">
                        <i class="fas fa-exclamation-triangle"></i>Please fill in all fields.
                    </div>
                    
                    <!-- Link to registration page -->
                    <div class="createAccount">
                        <p>Not Registered?</p>
                        <a href="register.php">Create an Account</a>
                    </div>
                </form>
            </main>
            
            <!-- Footer with copyright notice -->
            <footer>
                <p>© Copyright Inanimus Web Development 2024</p>
            </footer>
        </div>
        
        <!-- Custom JavaScript for form validation and interactivity -->
        <script src="login.js"></script>
    </body>
</html>