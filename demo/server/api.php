<?php
// Assuming you have a MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "referraldb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select all records from the "patients" table
$sql = "SELECT * FROM patients";
$result = $conn->query($sql);

// Fetch data and return as JSON
$patients = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($patients);
?>
