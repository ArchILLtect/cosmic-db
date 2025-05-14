<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    session_start();

    // Not logged in, redirected to unauthorizedaccess.php script
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_access_privileges']))
    {
        header("Location: unauthorizedaccess.php");
        exit();
    }

    // DEFAULT: If $required_access_level not set, assume 'user'
    if (!isset($required_access_level)) {
        $required_access_level = 'user';
    }

    // Privilege levels
    $privilege_order = [
        'anonymous' => 0,
        'user' => 1,
        'admin' => 2
    ];

    // If current user's privilege < required page privilege, deny access
    if ($privilege_order[$_SESSION['user_access_privileges']] < $privilege_order[$required_access_level]) {
        header("Location: unauthorizedaccess.php");
        exit();
    }