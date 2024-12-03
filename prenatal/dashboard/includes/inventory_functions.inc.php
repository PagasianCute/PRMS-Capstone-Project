<?php
// Include the database connection file
include_once '../../db/db_conn.php';

function getPaginatedPatients($page, $itemsPerPage) {
    global $conn, $fclt_id;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch paginated rows from the "patients" table
    $sql = "SELECT  booklet.*,
    staff.fname AS staff_fname, 
    staff.mname AS staff_mname, 
    staff.lname AS staff_lname
    FROM 
        booklet
    INNER JOIN 
    staff ON booklet.birth_attendant = staff.staff_id
    WHERE booklet.fclt_id = $fclt_id
    GROUP BY booklet.case_number
    ORDER BY booklet.case_number DESC
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
    global $conn, $fclt_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM booklet WHERE fclt_id = $fclt_id";
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
