<?php

use Hotel\User;

// Include bootstrap file
require_once __DIR__ .'/../../boot/boot.php';

// Check if request method is POST
if(strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
    header('Location: /Project/public/assets/login.php');
    return;
}

// Initialize User service
$user = new User();

// Get email and password from request
$email = $_REQUEST['email'] ?? '';
$password = $_REQUEST['password'] ?? '';

// Verify credentials
try {
    // First check if user exists with this email
    $userInfo = $user->getByEmail($email);
    
    if (!$userInfo) {
        // User not found
        header('Location: /Project/public/assets/login.php?error=invalid');
        return;
    }
    
    // Verify password
    if ($user->verify($email, $password)) {
        // Successful login
        
        // Generate token
        $token = $user->generateToken($userInfo['user_id']);
        
        // Set cookie
        setcookie('user_token', $token, time() + (30 * 24 * 60 * 60), '/');
        
        // Set current user ID for the session
        User::setCurrentUserId($userInfo['user_id']);
        
        // Redirect to index page
        header('Location: /Project/public/assets/index.php');
    } else {
        // Invalid password
        header('Location: /Project/public/assets/login.php?error=invalid');
    }
} catch (Exception $e) {
    // Handle exceptions
    header('Location: /Project/public/assets/login.php?error=system');
}