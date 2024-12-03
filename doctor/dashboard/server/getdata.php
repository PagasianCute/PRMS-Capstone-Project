<?php
$servername = "localhost";
$username = "root";  // Replace with your MySQL username
$password = "";  // Replace with your MySQL password
$dbname = "referraldb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM patients LEFT JOIN patiens_schedule ON patiens_schedule.patients_id = patients.id";
$result = $conn->query($sql);

$events = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = array(
            'id' => $row['id'],
            'title' => $row['lname'],
            'start' => $row['date'],
            'type' => $row['type'],
            // Add more fields as needed
        );
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($events);
?>
