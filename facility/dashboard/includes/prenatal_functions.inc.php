<?php
// Include the database connection file
include_once '../../db/db_conn.php';

function getPaginatedPatients($page, $itemsPerPage) {
    global $conn, $fclt_id;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT * FROM patients WHERE fclt_id = $fclt_id LIMIT $offset, $itemsPerPage";
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
    global $conn, $fclt_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM patients WHERE fclt_id = $fclt_id";
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
function getPrenatalRecords($page, $itemsPerPage) {
    global $conn;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT 
                facilities.*, 
                COALESCE(prenatal_counts.prenatal_records_count, 0) AS prenatal_records_count,
                COALESCE(patients_counts.patients_count, 0) AS patients_count
            FROM facilities
            LEFT JOIN (
                SELECT fclt_id, COUNT(*) AS prenatal_records_count
                FROM prenatal_records
                GROUP BY fclt_id
            ) AS prenatal_counts ON facilities.fclt_id = prenatal_counts.fclt_id

            LEFT JOIN (
                SELECT fclt_id, COUNT(*) AS patients_count
                FROM patients
                GROUP BY fclt_id
            ) AS patients_counts ON facilities.fclt_id = patients_counts.fclt_id

            WHERE facilities.verification = 'Verified' AND facilities.fclt_type = 'Birthing Home'
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


function getTotalPrenatal() {
    global $conn, $fclt_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM facilities WHERE fclt_type = 'Birthing Home' AND verification = 'Verified'";
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

function getGiveBirth() {
    global $conn;
    // Get the current month
    $currentMonth = date('m');

    $query = "SELECT facilities.fclt_name, facilities.img_url, COUNT(*) as count
	FROM patients_details
    INNER JOIN patients ON patients_details.patients_id = patients.id
    INNER JOIN facilities ON patients.fclt_id = facilities.fclt_id
    WHERE facilities.verification = 'Verified'
    AND facilities.fclt_type = 'Birthing Home'
    AND MONTH(patients_details.kailan_ako_manganganak) = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $currentMonth);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        // Fetch all rows into an associative array
        $referrals = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $referrals;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}

getGiveBirth();
