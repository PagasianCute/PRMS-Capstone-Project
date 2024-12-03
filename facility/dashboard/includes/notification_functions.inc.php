<?php
include 'db_conn.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['id'];

function getNotification() {
    global $conn, $fclt_id;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT * FROM referral_notification";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows into an associative array
        $notification = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $notification;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}