<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];

$output = array(); // Initialize $output as an array

$searchTerm = mysqli_real_escape_string($conn, $_POST['patient_name']);

$stmt = $conn->prepare("SELECT * FROM patients WHERE CONCAT(lname, ' ', fname, ' ', mname) LIKE ? AND fclt_id = ?");
$stmt->bind_param("si", $fullNameParam, $fclt_id);

$fullNameParam = "%$searchTerm%";

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'];
        $patientId = $row['id'];
        $output[] = array('value' => htmlspecialchars($fullName), 'patientId' => $patientId);
    }
}

$stmt->close();

echo json_encode($output);