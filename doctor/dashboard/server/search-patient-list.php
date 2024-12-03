<?php
include_once '../../../db/db_conn.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$fclt_id = $_SESSION['fcltid'];

if (isset($_POST['searchTerm'])) {
    $searchTerm = $_POST['searchTerm'];
    $fclt_id = $_SESSION['fcltid'];

    // Perform the query to fetch filtered rows from the "referral_forms" table
    $sql = "SELECT * 
        FROM doctors_referral
        INNER JOIN referral_records ON doctors_referral.rfrrl_id = referral_records.rfrrl_id
        INNER JOIN patients ON referral_records.patients_id = patients.id
        INNER JOIN facilities ON patients.fclt_id = facilities.fclt_id
        WHERE doctors_referral.fclt_id = $fclt_id
        AND (patients.fname LIKE '%$searchTerm%'
            OR patients.mname LIKE '%$searchTerm%'
            OR patients.lname LIKE '%$searchTerm%'
            OR facilities.fclt_name LIKE '%$searchTerm%'
            OR patients.barangay LIKE '%$searchTerm%')
        GROUP BY patients.id DESC";

    // If the search term is empty, limit the number of rows fetched
    if (empty($searchTerm)) {
        $sql .= " LIMIT 12"; // You can adjust the limit as needed
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch all rows into an associative array
        $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Output the filtered rows
        foreach ($patients as $key => $patient) {
            echo "<tr>";
            echo "<td>" . ($key + 1) . "</td>";
            echo "<td>{$patient['fname']}</td>";
            echo "<td>{$patient['mname']}</td>";
            echo "<td>{$patient['lname']}</td>";
            echo "<td>{$patient['fclt_name']}</td>";
            echo "<td>{$patient['barangay']}</td>";
            echo "<td>";
            echo "<a class='btn btn-primary table-btn' data-toggle='tooltip' data-placement='left' title='View Patient' href='view_patient.php?id={$patient['id']}' data-patient-id='{$patient['id']}' role='button'><i class='fi fi-rr-eye'></i></a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }
}
?>