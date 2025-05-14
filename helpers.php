<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 5/14/25
*/
function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
        exit;
    } else {
        echo "<script>window.location.href = '" . htmlspecialchars($url, ENT_QUOTES) . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=" . htmlspecialchars($url, ENT_QUOTES) . "'></noscript>";
        exit;
    }
}