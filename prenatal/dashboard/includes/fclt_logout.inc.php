<?php
// Start or resume the session
session_start();

    // First account logout
    unset($_SESSION["facilityaccount"]); // Unset the session variable for the first account
    unset($_SESSION["names"]); // Unset the first account's session variable
    session_destroy();
    header("Location: ../../../login/facility-login.php");
    exit();
