<!--    Author: Nick Hanson
        Version: 0.4
        Date: 4/15/25
-->
<?php
    session_start();

    // If the user is logged in, delete session variables and redirect to homepage
    if (isset($_SESSION['user_id']))
    {
        $_SESSION = array();
        session_destroy();
    }


    // Redirect to homepage
    $home_url = dirname($_SERVER['PHP_SELF']);
    header('Location: ' . $home_url);
    exit;