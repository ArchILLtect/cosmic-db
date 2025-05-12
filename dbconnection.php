<!--    Author: Nick Hanson
        Version: 0.1
        Date: 4/20/25
-->
<?php
    // Database connection constants
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'cosmic-db');
    define('DB_PORT', 3308);

    $dbc = mysqli_connect(  DB_HOST,
                            DB_USER,
                            DB_PASSWORD,
                            DB_NAME,
                            DB_PORT)
                        or trigger_error('Error connecting to MySQL server for'
                        .  DB_NAME, E_USER_ERROR)
    ;