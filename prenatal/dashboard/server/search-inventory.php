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
    $sql = "SELECT  booklet.*,
            staff.fname AS staff_fname, 
            staff.mname AS staff_mname, 
            staff.lname AS staff_lname
            FROM 
                booklet
            INNER JOIN 
            staff ON booklet.birth_attendant = staff.staff_id
            WHERE booklet.fclt_id = $fclt_id
            AND (booklet.case_number LIKE '%$searchTerm%'
                OR booklet.barangay LIKE '%$searchTerm%'
                OR booklet.birth_attendant LIKE '%$searchTerm%'
                OR booklet.fname LIKE '%$searchTerm%'
                OR booklet.mname LIKE '%$searchTerm%'
                OR booklet.lname LIKE '%$searchTerm%')
            ORDER BY booklet.case_number DESC";

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
            // Assuming the staff's full name is a combination of fname, mname, and lname
            $birthAttendantFullName = $patient['staff_lname'] . ', ' . $patient['staff_fname'] . ' ' . $patient['staff_mname'];
            echo "<tr>";
            echo "<td>{$patient['case_number']}</td>";
            echo "<td>{$patient['fname']}</td>";
            echo "<td>{$patient['mname']}</td>";
            echo "<td>{$patient['lname']}</td>";
            echo "<td>{$birthAttendantFullName}</td>";
            echo "<td>{$patient['barangay']}</td>";
            echo "<td>";
            echo "<button type='button' class='btn btn-primary table-btn viewRecord' value='{$patient['case_number']}' data-toggle='tooltip' data-placement='left' title='View Records'><i class='fi fi-rs-pencil'></i></button>";
            echo "<button type='button' class='btn btn-primary table-btn deletePatient' value='{$patient['case_number']}' data-toggle='tooltip' data-placement='left' title='Delete'><i class='fi fi-rs-trash'></i></button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }
}
?>
