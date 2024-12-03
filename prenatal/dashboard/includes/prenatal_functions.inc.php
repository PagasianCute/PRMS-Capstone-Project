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
    GROUP BY patients.id
    ORDER BY patients.id DESC
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

function getAllPaginatedPatients($page, $itemsPerPage) {
    global $conn, $fclt_id;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT patients.*, COUNT(prenatal_records.patients_id) AS prenatal_records_count, MAX(prenatal_records.date) AS latest_record_date
    FROM patients
    LEFT JOIN prenatal_records ON prenatal_records.patients_id = patients.id
    WHERE patients.fclt_id = $fclt_id
    GROUP BY patients.id
    ORDER BY patients.id DESC
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

function getPrenatalRecordsBirthing() {
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

function getPrenatalRecordsHospital() {
    global $conn, $fclt_id;
    $query = "SELECT facilities.fclt_name, facilities.fclt_id, COUNT(*) AS total_rows
    FROM prenatal_records
    INNER JOIN facilities ON facilities.fclt_id = prenatal_records.fclt_id
    GROUP BY facilities.fclt_name, facilities.fclt_id";
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

function getAppointment() {
    global $conn, $admin_id;

    // Get the current date
    $currentDate = date('Y-m-d');

    // Modify the query to include a condition for future appointments
    $query = "SELECT * FROM patients 
              INNER JOIN patient_schedule ON patients.id = patient_schedule.patients_id
              WHERE patient_schedule.date >= '$currentDate' AND patient_schedule.status = 'Upcoming' AND patient_schedule.staff_id = $admin_id";

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
