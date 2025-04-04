<?php 

use Hotel\User;

require_once __DIR__ .'/../../boot/boot.php';

if(strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
    header('Location: /Project/public/assets/register.php');
    return;
}

try {
    $user = new User();
    
    // First check if email already exists
    $existingUser = $user->getByEmail($_REQUEST['email']);
    if ($existingUser) {
        // Email already in use
        header('Location: /Project/public/assets/register.php?error=email_exists');
        return;
    }
    
    // Insert new user
    $success = $user->insert($_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email'], $_REQUEST['formPassword']);
    
    if (!$success) {
        // Insert failed
        header('Location: /Project/public/assets/register.php?error=insert_failed');
        return;
    }
    
    // Retrieve user
    $userInfo = $user->getByEmail($_REQUEST['email']);
    
    if (!$userInfo) {
        // User retrieval failed
        header('Location: /Project/public/assets/register.php?error=user_not_found');
        return;
    }
    
    // Generate Token
    $token = $user->generateToken($userInfo['user_id']);
    
    // Set cookie
    setcookie('user_token', $token, time() + (30 * 24 * 60 * 60), '/');
    
    // Set current user
    User::setCurrentUserId($userInfo['user_id']);
    
    // Return to home page
    header('Location: /Project/public/assets/index.php');
} catch (Exception $e) {
    // Log error
    error_log("Registration exception: " . $e->getMessage());
    header('Location: /Project/public/assets/register.php?error=system');
}