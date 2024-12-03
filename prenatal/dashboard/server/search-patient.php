<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];

$output = ""; // Initialize $output as an empty string

$searchTerm = mysqli_real_escape_string($conn, $_POST['patient_name']);

$query = mysqli_query($conn, "SELECT * FROM patients WHERE (lname LIKE '%$searchTerm%' OR fname LIKE '%$searchTerm%' OR mname LIKE '%$searchTerm%') AND fclt_id = '$fclt_id'");

if(mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_assoc($query)) {
        $fullName = $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'];
        $patientId = $row['id']; // Assuming 'patient_id' is the actual column name in your table
        $output .= '<option value="' . htmlspecialchars($fullName) . '" data-patient-id="' . $patientId . '">';
    }
}

echo $output;