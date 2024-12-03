<?php
include_once '../../db/db_conn.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getFacilitiesCard(){
    global $conn;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT f.fclt_name, f.verification, COUNT(s.staff_id) AS staff_count
    FROM facilities f
    LEFT JOIN staff s ON f.fclt_id = s.fclt_id
    GROUP BY f.fclt_id";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successfulasdsas
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

function getFacilities($page, $itemsPerPage) {
    global $conn;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT * FROM facilities ORDER BY fclt_id DESC LIMIT $offset, $itemsPerPage";
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
function getTotalStaff() {
    global $conn;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM facilities";
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