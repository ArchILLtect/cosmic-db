<!--    Author: Nick Hanson
        Version: 0.1
        Date: 4/20/25
-->
<?php
    // Database connection constants
    define('DB_HOST', 'localhost');
    define('DB_USER', 'bdukyxmy_nick');
    define('DB_PASSWORD', 'a5D5uiAJd28YsLF');
    define('DB_NAME', 'bdukyxmy_cosmicdb');

    $dbc = mysqli_connect(  DB_HOST,
                            DB_USER,
                            DB_PASSWORD,
                            DB_NAME)
                        or trigger_error('Error connecting to MySQL server for'
                        .  DB_NAME, E_USER_ERROR)
    ;