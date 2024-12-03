<?php
//$receiver_id = $_GET["contact-id"];

function contacts() {
    global $conn, $fclt_id;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT * FROM `facilities`";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows into an associative array
        $contacts = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $contacts;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}

function messages() {
    global $conn, $fclt_id;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT * FROM `messages`";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows into an associative array
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $messages;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}
