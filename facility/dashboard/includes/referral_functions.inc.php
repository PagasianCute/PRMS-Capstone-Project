<?php
include 'dbh.inc.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$fclt_id = $_SESSION['fcltid'];
$fclt_type = $_SESSION["fclttype"];

function myReferrals($page, $itemsPerPage) {
    global $conn, $fclt_id;

    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT referral_forms.*, referral_records.*, facilities.*
    FROM referral_forms
    INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital
    WHERE referral_records.fclt_id = $fclt_id ORDER BY referral_forms.id DESC LIMIT $offset, $itemsPerPage";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function getTotalReferrals() {
    global $conn, $fclt_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM referral_records INNER JOIN referral_forms ON referral_forms.id = referral_records.rfrrl_id WHERE fclt_id = $fclt_id";
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

function displayAllReferralsPending() {
    global $conn, $fclt_id; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT referral_forms.*, referral_records.*, facilities.*, referral_transaction.status
        FROM referral_forms
        LEFT JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
        LEFT JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
        LEFT JOIN referral_transaction ON referral_records.rfrrl_id = referral_transaction.rfrrl_id
        WHERE referral_records.referred_hospital = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $fclt_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

    // Check if the query was successful
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

function ProvHosPendingReferrals() {
    global $conn, $fclt_id; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT
    facilities_from.fclt_name AS from_facility_name,
    facilities_to.fclt_name AS referred_facility_name,
    referral_records.*, referral_forms.*
    FROM
        referral_records
    INNER JOIN
        facilities AS facilities_from ON referral_records.fclt_id = facilities_from.fclt_id
    INNER JOIN
        facilities AS facilities_to ON referral_records.referred_hospital = facilities_to.fclt_id
    INNER JOIN
        referral_forms ON referral_forms.id = referral_records.rfrrl_id
            WHERE referral_records.referred_hospital = ? AND referral_records.status = 'Pending' ORDER BY referral_records.id ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $fclt_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

    // Check if the query was successful
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

function HospitalPendingReferrals() {
    global $conn, $fclt_id; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT
    facilities_from.fclt_name AS from_facility_name,
    facilities_to.fclt_name AS referred_facility_name,
    referral_records.*, referral_forms.*
    FROM
        referral_records
    INNER JOIN
        facilities AS facilities_from ON referral_records.fclt_id = facilities_from.fclt_id
    INNER JOIN
        facilities AS facilities_to ON referral_records.referred_hospital = facilities_to.fclt_id
    INNER JOIN
        referral_forms ON referral_forms.id = referral_records.rfrrl_id
    WHERE
        (referral_records.referred_hospital = $fclt_id AND referral_records.status = 'Pending')
        OR referral_records.status = 'Declined'
    ORDER BY
        referral_records.id ASC";
    
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo "Error preparing the statement: " . mysqli_error($conn);
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        // Check if the query was successful
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
    }
    
    // Return an empty array in case of an error
    return array();    
}


function displayAllReferralTransaction($page, $itemsPerPage) {
    global $conn, $fclt_id; // Access the existing database connection

    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT referral_transaction.*,facilities.*,staff.lname,staff.fname,staff.mname,referral_forms.name
    FROM referral_records
    INNER JOIN referral_transaction ON referral_transaction.rfrrl_id = referral_records.rfrrl_id
    INNER JOIN referral_forms ON referral_forms.id = referral_transaction.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
    LEFT JOIN staff ON referral_transaction.receiving_officer = staff.staff_id
    WHERE referral_transaction.fclt_id = '$fclt_id' ORDER BY referral_transaction.id DESC
    LIMIT $offset, $itemsPerPage";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function getTotalReferralsTransaction() {
    global $conn, $fclt_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM referral_transaction WHERE fclt_id = $fclt_id";
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

function referrals_audit() {
    global $conn, $fclt_id; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT * FROM referral_transaction";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function getAllReferrals() {
    global $conn; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT * FROM referral_forms";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function referrals() {
    global $conn; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT referral_forms.*, facilities.fclt_name FROM referral_forms
    INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON referral_records.fclt_id = facilities.fclt_id";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function minireferrals() {
    global $conn, $fclt_id, $fclt_type;

    if($fclt_type == 'Birthing Home'){
        $sql = "SELECT *
    FROM referral_records
    INNER JOIN referral_forms ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital
    WHERE referral_records.fclt_id = '$fclt_id' ORDER BY referral_records.id DESC
    LIMIT 4";
    }else if($fclt_type == 'Hospital'){
        $sql = "SELECT *
        FROM referral_records
        INNER JOIN referral_forms ON referral_forms.id = referral_records.rfrrl_id
        INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
        WHERE referred_hospital = '$fclt_id' AND referral_records.status = 'Pending' OR referral_records.status = 'Declined'
        ORDER BY referral_records.id DESC LIMIT 4";
        } else if($fclt_type == 'Provincial Hospital'){
            $sql = "SELECT *
            FROM referral_records
            INNER JOIN referral_forms ON referral_forms.id = referral_records.rfrrl_id
            INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
            WHERE referred_hospital = '$fclt_id' AND referral_records.status = 'Pending'
            ORDER BY referral_records.id DESC LIMIT 4";
            }
    
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function prenatals() {
    global $conn, $fclt_id;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT p.fclt_id, COUNT(*) AS row_count, fclt_name FROM patients AS p INNER JOIN facilities AS f ON p.fclt_id = f.fclt_id GROUP BY p.fclt_id ORDER BY p.fclt_id";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows into an associative array
        $prenatals = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $prenatals;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}

function referral_transactions() {
    global $conn, $fclt_id;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT * FROM referral_transaction";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function newArrival() {
    global $conn, $fclt_id;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT referral_forms.*, referral_records.fclt_id, facilities.fclt_name, referral_transaction.*
            FROM referral_forms
            INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
            INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
            INNER JOIN referral_transaction ON referral_transaction.rfrrl_id = referral_forms.id
            WHERE referral_transaction.fclt_id = '$fclt_id' AND referral_transaction.Arrival = 'Arriving'";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function doctors() {
    global $conn; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT *
    FROM staff
    INNER JOIN facilities ON facilities.fclt_id = staff.fclt_id
    WHERE role = 'Doctor'
    ORDER BY CASE WHEN staff.status = 'online' THEN 1 ELSE 2 END, staff.staff_id ASC";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows into an associative array
        $doctors = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $doctors;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}

function forReferrralPatients() {
    global $conn,$fclt_id; // Access the existing database connection

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT 
    patients.fname AS patient_fname,
    patients.mname AS patient_mname,
    patients.lname AS patient_lname,
    staff.fname AS staff_fname,
    staff.mname AS staff_mname,
    staff.lname AS staff_lname,
    facilities.fclt_name as facility_name,
    referred_facility.fclt_name as from_facility,
    staff.img
FROM 
    patients
    INNER JOIN for_referral_patients ON patients.id = for_referral_patients.patients_id
    INNER JOIN facilities ON for_referral_patients.referred_hospital = facilities.fclt_id
    INNER JOIN staff ON for_referral_patients.staff_id = staff.staff_id
    INNER JOIN facilities AS referred_facility ON for_referral_patients.fclt_id = referred_facility.fclt_id
    WHERE for_referral_patients.fclt_id = $fclt_id AND for_referral_patients.status = 0";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows into an associative array
        $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);

        // Return the array of referrals
        return $patients;
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Return an empty array in case of an error
    return array();
}

function getforReferralPatients($page, $itemsPerPage) {
    global $conn, $fclt_id;

    $offset = ($page - 1) * $itemsPerPage;

    // Perform the query to fetch all rows from the "referrals" table
    $sql = "SELECT 
            for_referral_patients.*,
            patients.fname AS patient_fname,
            patients.mname AS patient_mname,
            patients.lname AS patient_lname,
            facilities.*,
            staff.fname AS staff_fname,
            staff.mname AS staff_mname,
            staff.lname AS staff_lname
            FROM 
            for_referral_patients
            INNER JOIN patients ON for_referral_patients.patients_id = patients.id
            INNER JOIN facilities ON for_referral_patients.referred_hospital = facilities.fclt_id
            INNER JOIN staff ON for_referral_patients.staff_id = staff.staff_id
    WHERE for_referral_patients.fclt_id = $fclt_id AND for_referral_patients.status = 0 ORDER BY for_referral_patients.id DESC LIMIT $offset, $itemsPerPage";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
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

function getTotalForReferralPatients() {
    global $conn, $fclt_id;

    // Perform the query to get the total number of patients
    $sql = "SELECT COUNT(*) as total FROM for_referral_patients WHERE fclt_id = $fclt_id";
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