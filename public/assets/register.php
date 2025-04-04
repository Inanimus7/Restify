<?php
require __DIR__ . '/../../boot/boot.php';

use Hotel\User;

if (!empty(User::getCurrentUserId())) {
    header('Location:/Project/public/assets/index.php');
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Restify</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="styling/register.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    <div class="img-background"></div>
    <header>
        <div class="restify-title">
            <a href="index.php">Restify.</a>
        </div>
    </header>
    <main>
        <div class="form-container">
            <form method="post" action="../actions/register.php" class="registration-form" autocomplete="off">
                <h1 class="form-header">Sign Up</h1>
                <div class="form-group">
                    <input id="firstName" type="text" maxlength="26" name="first_name" pattern="[A-Za-z]{1,26}" placeholder=" " required>
                    <label for="firstName"><span style="color: red;">* </span>First Name</label>
                </div>
                <div class="form-group">
                    <input id="lastName" type="text" maxlength="26" name="last_name" pattern="[A-Za-z]{1,26}" placeholder=" " required>
                    <label for="lastName"><span style="color: red;">* </span>Last Name</label>
                </div>
                <div class="form-group">
                    <input id="email" type="email" placeholder=" " required name="email">
                    <label for="email"><span style="color: red;">* </span>E-mail</label>
                    <div class="email-error d-none">Please enter a valid e-mail.</div>
                </div>
                <div class="form-group">
                    <input name="formPassword" id="formPassword" type="password" placeholder=" " required>
                    <label for="formPassword"><span style="color: red;">* </span>Password</label>
                    <div class="password-error d-none">Your password must contain at least 8 characters.</div>
                </div>
                <div class="form-group bottom-margin">
                    <input id="confirmPassword" type="password" placeholder=" " required>
                    <label for="confirmPassword"><span style="color: red;">* </span>Confirm Password</label>
                    <div class="passwordMatch-error d-none">Please make sure your passwords match.</div>
                </div>
                <div class="form-group">
                    <div class="checkbox-container">
                        <label for="showPassword">Show Password</label>
                        <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                    </div>
                </div>
                <div class="form-group button-position">
                    <button type="submit" id="sign-up-btn">Sign Up</button>
                    <?php if (!empty($_GET['error'])) { ?>
                        <div class="alert alert-danger alert-styled-left">
                            <?php 
                                $errorMsg = "Registration Error";
                                switch($_GET['error']) {
                                    case 'email_exists':
                                        $errorMsg = "This email is already registered";
                                        break;
                                    case 'insert_failed':
                                        $errorMsg = "Failed to create account. Please try again.";
                                        break;
                                    case 'system':
                                        $errorMsg = "System error. Please try again later.";
                                        break;
                                }
                                echo $errorMsg;
                            ?>
                        </div>
                    <?php } ?>
                    <div id="formErrorMessage" class="d-none formErrorMessage">Please fill all required fields.</div>
                </div>
                <div class="form-group">
                    <div class="login-link">
                        <p>Already have an account?</p>
                        <a href="login.php">Login here</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <footer>
        <p>© Copyright Inanimus Web Development 2024</p>
    </footer>
    <script src="register.js"></script>
</body>
</html>