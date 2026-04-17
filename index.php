<?php

// init session
session_start();

$timeout = 1800; // 30 menit
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Root API Bootloader
include 'Bootloader.php';

$bootloader = new Bootloader();
$bootloader->run();

?>