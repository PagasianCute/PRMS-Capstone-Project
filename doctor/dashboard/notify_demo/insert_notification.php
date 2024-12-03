<?php
include 'db_conn.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    $sql = "INSERT INTO notify_demo (message) VALUES ('$message')";
    if (mysqli_query($conn, $sql)) {
        echo "Notification inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}header("Location:notify.php");
exit();
?>
