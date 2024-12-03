<?php
// Include the database connection file
include_once '../../db/db_conn.php';

function getPaginatedPatients($page, $itemsPerPage) {
    global $conn;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT 
    prenatal_records.*,
    patients.fname AS patient_fname,
    patients.mname AS patient_mname,
    patients.lname AS patient_lname,
    patients.barangay,
    staff.fname AS staff_fname,
    staff.mname AS staff_mname,
    staff.lname AS staff_lname
    FROM prenatal_records
    INNER JOIN patients ON prenatal_records.patients_id = patients.id
    INNER JOIN staff ON patients.staff_id = staff.staff_id LIMIT $offset, $itemsPerPage";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch all rows into an associative array
        $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of patients
        return $patients;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
        return array();
    }
}

// Function to get the total number of patients
function getTotalPatients() {
    global $conn;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM prenatal_records INNER JOIN patients ON prenatal_records.patients_id = patients.id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch the total count
        $row = mysqli_fetch_assoc($result);

        // Free the result set
        mysqli_free_result($result);

        // Return the total count
        return $row['total'];
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
        return 0;
    }
}

function getPatientDetails($conn, $patientID) {
    $query = "SELECT patients.id, patients_details.* FROM patients LEFT JOIN patients_details ON patients.id = patients_details.patients_id WHERE id = $patientID";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);

    // Close the result and return the row
    mysqli_free_result($result);

    return $row;
}
