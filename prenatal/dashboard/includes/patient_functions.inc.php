<?php
// Include the database connection file
include_once '../../db/db_conn.php';

function getPaginatedPatients($page, $itemsPerPage) {
    global $conn, $admin_id;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT patients.*, COUNT(prenatal_records.patients_id) AS prenatal_records_count, MAX(prenatal_records.date) AS latest_record_date
    FROM patients
    LEFT JOIN prenatal_records ON prenatal_records.patients_id = patients.id
    WHERE patients.staff_id = $admin_id
    GROUP BY patients.id DESC
    LIMIT $offset, $itemsPerPage";
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
    global $conn, $admin_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM patients WHERE staff_id = $admin_id";
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

function getPrenatalRecords() {
    global $conn, $fclt_id;
    $query = "SELECT * 
        FROM `patients`
        INNER JOIN prenatal_records ON prenatal_records.patients_id = patients.id
        WHERE prenatal_records.fclt_id = $fclt_id
        ORDER BY prenatal_records.prenatal_records_id LIMIT 4";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($conn));
    }

    $records = [];

    // Fetch all rows and store them in an array
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }

    // Close the result
    mysqli_free_result($result);

    return $records;
}

function getAttachments() {
    global $conn;
    $patient_id = $_GET['id'];
    $query = "SELECT * FROM patients_attachments WHERE patients_id = $patient_id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($conn));
    }

    $attachments = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $attachments[] = $row;
    }

    // Close the result and return the array of attachments
    mysqli_free_result($result);

    return $attachments;
}

function getAppointment() {
    global $conn;
    $patient_id = $_GET['id'];

    $query = "SELECT * FROM patient_schedule WHERE patients_id = $patient_id ORDER BY schedule_id DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($conn));
    }

    $appointment = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $appointment[] = $row;
    }

    // Close the result and return the array of appointment
    mysqli_free_result($result);

    return $appointment;
}