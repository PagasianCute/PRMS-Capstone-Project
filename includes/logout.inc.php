<?php
// Start or resume the session
session_start();

include_once '../db/db_conn.php'; // Assuming this file contains the database connection logic

if(isset($_SESSION["facilityaccount"])) {
    // Set the status to 'Offline'
    $status = 'Offline';

    // Using your existing database connection
    $conn = connectToDatabase();

    // Update the user's status in the database
    $stmt = $conn->prepare('UPDATE staff SET status = ? WHERE staff_id = ?');
    $stmt->bind_param('si', $status, $_SESSION["usersid"]);
    $stmt->execute();

    // Check for success or failure
    if ($stmt->affected_rows > 0) {
        echo "Update successful!";
    } else {
        echo "Update failed or no rows were affected.";
    }

    // Unset and destroy the session variables for the first account
    unset($_SESSION["facilityaccount"]);
    unset($_SESSION["names"]);

    // Redirect to the login page
    header("Location: ../login/facility-login.php");
    session_destroy();
    exit();
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: ../login/facility-login.php");
    exit();
}
?>
