<?php
include_once '../../db/db_conn.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$fclt_id = $_SESSION['fcltid'];

function myReferrals($page, $itemsPerPage)
{
    global $conn, $fclt_id;

    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT 
    referral_forms.*, 
    referral_records.*, 
    hospital_facilities.fclt_name AS referred_hospital_name,
    fclt_facilities.fclt_name AS fclt_name
    FROM 
        referral_forms
    INNER JOIN 
        referral_records ON referral_forms.id = referral_records.rfrrl_id
    LEFT JOIN 
        facilities AS hospital_facilities ON hospital_facilities.fclt_id = referral_records.referred_hospital
    LEFT JOIN 
        facilities AS fclt_facilities ON fclt_facilities.fclt_id = referral_records.fclt_id
    WHERE fclt_facilities.fclt_id = $fclt_id OR referral_records.referred_hospital = $fclt_id ORDER BY referral_forms.id DESC LIMIT $offset, $itemsPerPage";
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

function getTotalReferrals()
{
    global $conn;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM referral_records INNER JOIN referral_forms ON referral_forms.id = referral_records.rfrrl_id";
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
