<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

if (isset($_GET['rffrl_id'])) {
    $rffrl_id = mysqli_real_escape_string($conn, $_GET['rffrl_id']);

    $query = "SELECT referral_forms.*, referral_records.*, facilities.*
    FROM referral_forms
    INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital
    WHERE referral_forms.id = '$rffrl_id'";
    $query_run = mysqli_query($conn, $query);

    $queryclumn = "SHOW COLUMNS FROM referral_forms";
    $querycolumn_run = mysqli_query($conn, $queryclumn);

    $querytransactions = "SELECT *
	FROM referral_transaction
    INNER JOIN facilities ON referral_transaction.fclt_id = facilities.fclt_id
    WHERE rfrrl_id = '$rffrl_id'";
    $querytransactions_run = mysqli_query($conn, $querytransactions);

    $queryData = mysqli_fetch_array($query_run);

    $columnData = [];
    while ($row = mysqli_fetch_assoc($querycolumn_run)) {
        $columnNames[] = $row['Field'];
    }

    $querytransactions_run = mysqli_query($conn, $querytransactions);

    $querytransactions_data = [];
    while ($row = mysqli_fetch_array($querytransactions_run)) {
        $querytransactions_data[] = $row;
    }

    $res = [
        'status' => 200,
        'message' => 'Data fetched successfully',
        'data' => $queryData,
        'column_data' => $columnNames,
        'transactions' => $querytransactions_data,
    ];

    echo json_encode($res);
}

if (isset($_POST['add_patient'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $mname = mysqli_real_escape_string($conn, $_POST['mname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    if ($fname == NULL || $lname == NULL || $contactNum == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Field is mandatory'
        ];
        echo json_encode($res);
        return false;
    }
    $query = "INSERT INTO patients (fname, mname, lname, contact, address, fclt_id) VALUES ('$fname',  '$mname', '$lname',  '$contactNum', '$address' , '$fclt_id')";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Patient added successfully'
        ];
        echo json_encode($res);
        return false;
    } else {
        // At least one query failed
        $res = [
            'status' => 500,
            'message' => 'Patient not created successfully'
        ];
        echo json_encode($res);
        return false;
    }
    header('Content-Type: application/json');
    echo json_encode($responseArray);
}

if (isset($_POST['login'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);

    if (empty($uid) || empty($pwd)) {
        $res = [
            'status' => 422,
            'message' => 'Username and password are mandatory'
        ];
        echo json_encode($res);
        return false;
    }

    $query = "SELECT * FROM users WHERE usersUid = '$uid'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $user = mysqli_fetch_assoc($query_run);
        if ($user) {
            // Continue with the password verification using the correct column name
            $hashed_password = $user['usersPwd']; // Use the correct column name

            // Verify the provided password against the stored hash
            if (password_verify($pwd, $hashed_password)) {
                $_SESSION["second_account"] = true;
                $_SESSION["usersid"] = $user["usersId"];
                $_SESSION["usersname"] = $user["usersName"];
                $_SESSION["usersuid"] = $user["usersUid"];
                $_SESSION["usersrole"] = $user["usersrole"];
                $_SESSION["email"] = $user["usersEmail"];
                $_SESSION["usersimg"] = $user["usersImg"];

                // Return a JSON response for a successful login
                $res = [
                    'status' => 200,
                    'message' => 'Login successful',
                    // Include any additional data you want to return
                    'user_id' => $user["usersId"],
                    'user_name' => $user["usersName"]
                ];
                echo json_encode($res);
            } else {
                $res = [
                    'status' => 401,
                    'message' => 'Invalid username or password'
                ];
                echo json_encode($res);
            }
        } else {
            $res = [
                'status' => 401,
                'message' => 'Invalid username or password'
            ];
            echo json_encode($res);
        }
    } else {
        // Database query error
        $res = [
            'status' => 500,
            'message' => 'Database error. Unable to perform login.'
        ];
        echo json_encode($res);
    }
}

if (isset($_POST['accept_referral'])) {
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rffrl_id']);
    $fromFcltId = mysqli_real_escape_string($conn, $_POST['fclt_id']);

    // Select statement to retrieve data before updating
    $select_query = "SELECT latitude, longitude FROM facilities WHERE fclt_id=?";
    $select_stmt = mysqli_prepare($conn, $select_query);
    mysqli_stmt_bind_param($select_stmt, "s", $fromFcltId);
    $select_query_run = mysqli_stmt_execute($select_stmt);

    if (!$select_query_run) {
        // Handle the select query error
        $res = [
            'status' => 500,
            'message' => 'Error executing select query: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    // Fetch data from the select query result
    $from_data = mysqli_fetch_assoc(mysqli_stmt_get_result($select_stmt));

    // Close the statement to free the result set
    mysqli_stmt_close($select_stmt);

    // Second SELECT statement
    $select_query2 = "SELECT latitude, longitude FROM facilities WHERE fclt_id=?";
    $select_stmt2 = mysqli_prepare($conn, $select_query2);
    mysqli_stmt_bind_param($select_stmt2, "s", $fclt_id);
    $select_query_run2 = mysqli_stmt_execute($select_stmt2);

    if (!$select_query_run2) {
        // Handle the second select query error
        $res = [
            'status' => 500,
            'message' => 'Error executing second select query: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    // Fetch data from the second select query result
    $to_data = mysqli_fetch_assoc(mysqli_stmt_get_result($select_stmt2));

    // Close the second statement to free the result set
    mysqli_stmt_close($select_stmt2);

    // Function to calculate Haversine distance
    function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Radius of the Earth in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $R * $c;

        return $distance;
    }

    // Function to estimate travel time
    function estimateTravelTime($lat1, $lon1, $lat2, $lon2, $modeOfTransportation) {
        $distance = haversineDistance($lat1, $lon1, $lat2, $lon2);

        $speeds = [
            'walking' => 5,
            'cycling' => 20,
            'driving' => 60,
            'ambulance' => 80 // Adjust as needed based on local regulations and conditions
        ];

        $speed = $speeds[$modeOfTransportation] ?? $speeds['driving'];

        $travelTime = $distance / $speed;

        return $travelTime;
    }

    // Extract relevant data for the insert queries
    $latitude1 = $from_data['latitude'];
    $longitude1 = $from_data['longitude'];
    $latitude2 = $to_data['latitude'];
    $longitude2 = $to_data['longitude'];
    $estimatedTime = estimateTravelTime($latitude1, $longitude1, $latitude2, $longitude2, 'ambulance');

    // Check if $estimatedTime is a numeric value
    if (!is_numeric($estimatedTime)) {
        $res = [
            'status' => 500,
            'message' => 'Error calculating estimated time'
        ];
        echo json_encode($res);
        return false;
    }

    $estimatedTimeSeconds = round($estimatedTime * 60 * 60);

    // Continue with the rest of your code for updating tables
    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("h:i A");

    $currentTime = new DateTime();
    $arrivalTime = new DateTime($currentTime->format('Y-m-d H:i:s'));
    $arrivalTime->add(new DateInterval('PT' . $estimatedTimeSeconds . 'S'));

    $expectedTime = $arrivalTime->format('h:i A');

    $query = "UPDATE referral_records SET status ='Accepted' WHERE rfrrl_id=?";
    $query_stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($query_stmt, "s", $rfrrl_id);
    $query_run = mysqli_stmt_execute($query_stmt);

    if (!$query_run) {
        // Handle the select query error
        $res = [
            'status' => 500,
            'message' => 'Error executing select query: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    mysqli_stmt_close($query_stmt);

    // Insert into referral_transaction using extracted data
    $second_query = "INSERT INTO referral_transaction (fclt_id, rfrrl_id, status, date, time, reason, arrival, expected_arrival) VALUES (?, ?, 'Accepted', ?, ?, 'NULL', 'Arriving', ?)";
    $second_query_stmt = mysqli_prepare($conn, $second_query);

    if (!$second_query_stmt) {
        // Handle the prepare statement error
        $res = [
            'status' => 500,
            'message' => 'Error preparing second query: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    mysqli_stmt_bind_param($second_query_stmt, "sssss", $fclt_id, $rfrrl_id, $date, $time,  $expectedTime);
    $second_query_run = mysqli_stmt_execute($second_query_stmt);

    if (!$second_query_run) {
        // Handle the execute statement error
        $res = [
            'status' => 500,
            'message' => 'Error executing second query: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    mysqli_stmt_close($second_query_stmt);

    // Insert into referral_notification using extracted data
    $third_query = "INSERT INTO referral_notification (message, rfrrl_id, fclt_id, date, time) VALUES ('Referral Accepted', ?, ?, ?, ?)";
    $third_query_stmt = mysqli_prepare($conn, $third_query);
    mysqli_stmt_bind_param($third_query_stmt, "ssss", $rfrrl_id, $fclt_id, $date, $time);
    $third_query_run = mysqli_stmt_execute($third_query_stmt);

    if (!$third_query_run) {
        // Handle the select query error
        $res = [
            'status' => 500,
            'message' => 'Error executing select query: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    mysqli_stmt_close($third_query_stmt);

    if ($query_run && $second_query_run && $third_query_run) {
        // Both queries executed successfully
        $res = [
            'status' => 200,
            'message' => 'Both queries executed successfully',
            'lat1' => $latitude1,
            'lon1' => $longitude1,
            'lat2' => $latitude2,
            'lon2' => $longitude2   
        ];
        echo json_encode($res);
        //$pusher->trigger('my-channel', 'my-event', array('message' => 'Referral accepted by: ' . $fclt_name));
        return false;
    } else {
        // At least one query failed
        $res = [
            'status' => 500,
            'message' => 'One or both queries failed: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['decline_referral'])) {
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rffrl_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    if ($rfrrl_id == NULL || $reason == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Field is mandatory'
        ];
        echo json_encode($res);
        return false;
    }

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("h:i A");

    $query = "UPDATE referral_records SET status ='Declined' WHERE rfrrl_id='$rfrrl_id'";
    $query_run = mysqli_query($conn, $query);

    // Execute the second query
    $second_query = "INSERT INTO referral_transaction (fclt_id, rfrrl_id, status, date, time, reason) VALUES ('$fclt_id', '$rfrrl_id', 'Declined', '$date', '$time', '$reason')"; // Use $reason here
    $second_query_run = mysqli_query($conn, $second_query);

    $third_query = "INSERT INTO referral_notification (message, rfrrl_id, fclt_id, date, time) VALUES ('Referral Declined', '$rfrrl_id', '$fclt_id', '$date', '$time')";
    $third_query_run = mysqli_query($conn, $third_query);

    if ($query_run && $second_query_run && $third_query_run) {
        // Both queries executed successfully
        $res = [
            'status' => 200,
            'message' => 'Both queries executed successfully'
        ];
        echo json_encode($res);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'Referral declined by: ' . $fclt_name));
        return false;
    } else {
        // At least one query failed
        $res = [
            'status' => 500,
            'message' => 'One or both queries failed'
        ];
        echo json_encode($res);
        return false;
    }
}


if (isset($_POST['restore_referral'])) {
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rffrl_id']);

    if ($rfrrl_id == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Field is mandatory'
        ];
        echo json_encode($res);
        return false;
    }

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("h:i A");

    $query = "UPDATE referral_records SET status = 'Pending' WHERE rfrrl_id='$rfrrl_id'";
    $query_run = mysqli_query($conn, $query);

    // Execute the second query
    $second_query = "DELETE FROM referral_transaction WHERE rfrrl_id = $rfrrl_id";
    $second_query_run = mysqli_query($conn, $second_query);

    $third_query = "INSERT INTO referral_notification (message, rfrrl_id, fclt_id, date, time) VALUES ('Referral Declined', '$rfrrl_id', '$fclt_id', '$date', '$time')";
    $third_query_run = mysqli_query($conn, $third_query);

    if ($query_run && $second_query_run && $third_query_run) {
        // Both queries executed successfully
        $res = [
            'status' => 200,
            'message' => 'Both queries executed successfully'
        ];
        echo json_encode($res);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'Referral declined by: ' . $fclt_name));
        return false;
    } else {
        // At least one query failed
        $res = [
            'status' => 500,
            'message' => 'One or both queries failed'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['patient_arrived'])) {
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rffrl_id']);

    if ($rfrrl_id == NULL) {
        $res = [
            'status' => 422,
            'message' => 'no id found'
        ];
        echo json_encode($res);
        return false;
    }

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("h:i A");

    // Use prepared statement for the first query
    $query = "UPDATE referral_transaction SET arrival = 'Arrived', arrival_time = ? WHERE rfrrl_id = ? AND fclt_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        // Handle error
        $res = [
            'status' => 500,
            'message' => 'Failed to prepare statement: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sss", $time, $rfrrl_id, $fclt_id);
    $query_run = mysqli_stmt_execute($stmt);

    if (!$query_run) {
        // Handle query failure
        $res = [
            'status' => 500,
            'message' => 'First query failed: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        mysqli_stmt_close($stmt);
        return false;
    }

    mysqli_stmt_close($stmt);

    // Second query
    $second_query = "INSERT INTO referral_notification (message, rfrrl_id, fclt_id, date, time) VALUES ('Referral Declined', ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $second_query);

    if (!$stmt) {
        // Handle error
        $res = [
            'status' => 500,
            'message' => 'Failed to prepare second statement: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssss", $rfrrl_id, $fclt_id, $date, $time);
    $second_query_run = mysqli_stmt_execute($stmt);

    if (!$second_query_run) {
        // Handle second query failure
        $res = [
            'status' => 500,
            'message' => 'Second query failed: ' . mysqli_error($conn)
        ];
        echo json_encode($res);
        mysqli_stmt_close($stmt);
        return false;
    }

    mysqli_stmt_close($stmt);

    // Both queries executed successfully
    $res = [
        'status' => 200,
        'message' => 'Both queries executed successfully'
    ];
    echo json_encode($res);

    // $pusher->trigger('my-channel', 'my-event', array('message' => 'Referral declined by: ' . $fclt_name));
    return false;
}