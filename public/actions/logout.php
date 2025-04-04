<?php
setcookie('user_token', '', time() - 3600, '/'); // Expire the cookie
header('Location: /Project/public/assets/login.php');
exit;
?>