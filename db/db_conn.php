<?php

$servername = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "referraldb";

// Function to establish or reconnect to the MySQL server
function connectToDatabase() {
    global $servername, $dBUsername, $dBPassword, $dBName;
    return mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
}

// Initial connection
$conn = connectToDatabase();

// Attempt to reconnect if the connection is lost
if (!$conn) {
    $conn = connectToDatabase();
}

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
