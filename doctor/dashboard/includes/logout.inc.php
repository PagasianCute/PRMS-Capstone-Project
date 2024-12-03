<?php
// Start or resume the session
session_start();

    // First account logout
    unset($_SESSION["second_account"]); // Unset the session variable for the second account
    unset($_SESSION["useruid"]);
    header("Location: ../index.php");
    exit();
