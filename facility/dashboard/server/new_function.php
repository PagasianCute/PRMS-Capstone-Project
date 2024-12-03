<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$users_id = $_SESSION["usersid"];
$fclt_img = $_SESSION["fcltimg"];

if (isset($_GET['rffrl_id'])) {
    $rffrl_id = mysqli_real_escape_string($conn, $_GET['rffrl_id']);

    $query = "SELECT referral_forms.*, referral_records.*, facilities.*, referral_transaction.*, staff.*
    FROM referral_forms
    INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
    LEFT JOIN referral_transaction ON referral_records.rfrrl_id = referral_transaction.rfrrl_id
    LEFT JOIN staff ON referral_transaction.receiving_officer = staff.staff_id
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

    $third_query = "DELETE FROM doctors_referral WHERE rfrrl_id = $rfrrl_id";
    $third_query_run = mysqli_query($conn, $third_query);

    if ($query_run && $second_query_run && $third_query) {
        // Both queries executed successfully
        $res = [
            'status' => 200,
            'message' => 'Both queries executed successfully'
        ];
        echo json_encode($res);
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
    $patient_status_upon_arrival = mysqli_real_escape_string($conn, $_POST['patient_status_upon_arrival']);
    $receiving_officer = mysqli_real_escape_string($conn, $_POST['receiving_officer']);
    $arrival_date = mysqli_real_escape_string($conn, $_POST['arrival_date']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $users_id = $_SESSION["usersid"];

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
    $time = date("H:i");

    // Use prepared statement for the first query
    $query = "UPDATE referral_transaction SET arrival = 'Arrived', patient_status_upon_arrival = ?, receiving_officer = ?, arrival_date = ?, arrival_time = ? WHERE rfrrl_id = ? AND fclt_id = ?";
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

    mysqli_stmt_bind_param($stmt, "ssssss", $patient_status_upon_arrival, $users_id, $arrival_date, $arrival_time, $rfrrl_id, $fclt_id);
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

    $fclt_select_sql = "SELECT fclt_id FROM referral_records WHERE rfrrl_id = ?";
    $fclt_select_stmt = $conn->prepare($fclt_select_sql);

    if (!$fclt_select_stmt) {
        // Handle the error
        $response = [
            'status' => 500,
            'message' => 'Error preparing SELECT statement: ' . $conn->error,
        ];
        echo json_encode($response);
        exit;
    }

    // Binding parameters to the SELECT prepared statement
    $fclt_select_stmt->bind_param("i", $rfrrl_id);

    // Execute the SELECT prepared statement
    $select_query_run = $fclt_select_stmt->execute();

    if ($select_query_run) {
        // Fetch data from the SELECT result set
        $select_result = $fclt_select_stmt->get_result();
        $select_row = $select_result->fetch_assoc();

        $new_fclt_id = $select_row['fclt_id'];
        $selected_data = (string) $select_row['fclt_id'];

        $message = $patient_status_upon_arrival;
        $type = "Patient Arrival";

        $notification_sql = "CALL notification (?, ?, ?, ?, ?, ?, ? , ?)";
        $notification_stmt = $conn->prepare($notification_sql);

        if (!$notification_stmt) {
            $response = [
                'status' => 500,
                'message' => 'Error preparing statement: ' . $conn->error,
            ];
            echo json_encode($response);
            exit;
        }

        // Binding parameters to the prepared statement
        $notification_stmt->bind_param("ssiisssi", $fclt_img, $message, $_SESSION['fcltid'], $new_fclt_id, $date, $time, $type, $users_id);
        // Execute the prepared statement
        $notification_query_run = $notification_stmt->execute();
        $data = $fclt_name;
        $pusher->trigger($selected_data,'patient_arrival',$data);

        // Close the SELECT result set
        $select_result->close();
    } else {
        // Handle the error executing the SELECT statement
        $response = [
            'status' => 500,
            'message' => 'Error executing SELECT statement: ' . $fclt_select_stmt->error,
        ];
        echo json_encode($response);
        exit;
    }

    // Close the SELECT prepared statement
    $fclt_select_stmt->close();

    // Both queries executed successfully
    $res = [
        'status' => 200,
        'message' => 'Both queries executed successfully'
    ];
    echo json_encode($res);

    // $pusher->trigger('my-channel', 'my-event', array('message' => 'Referral declined by: ' . $fclt_name));
}

if (isset($_POST['get-patient'])) {
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient-id']);

    if ($patient_id == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Patient not found'
        ];
    } else {
        $query = "SELECT * FROM patients WHERE id = '$patient_id'";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            $data = mysqli_fetch_assoc($query_run); // Fetching data as an associative array
            $res = [
                'status' => 200,
                'message' => 'Patient found',
                'data' => $data // Include fetched data in the response
            ];
        } else {
            // At least one query failed
            $res = [
                'status' => 500,
                'message' => 'Patient not found'
            ];
        }
    }

    echo json_encode($res);
}

if (isset($_POST['get-patient-referred'])) {
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient-id']);

    if ($patient_id == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Patient not found'
        ];
    } else {
        $query = "SELECT * FROM patients INNER JOIN for_referral_patients ON patients.id = for_referral_patients.patients_id WHERE for_referral_patients.id = '$patient_id'";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            $data = mysqli_fetch_assoc($query_run); // Fetching data as an associative array
            $res = [
                'status' => 200,
                'message' => 'Patient found',
                'data' => $data // Include fetched data in the response
            ];
        } else {
            // At least one query failed
            $res = [
                'status' => 500,
                'message' => 'Patient not found'
            ];
        }
    }

    echo json_encode($res);
}

if (isset($_GET['doctors_referral_id'])) {
    // Sanitize the input to prevent SQL injection
    $staff_id = mysqli_real_escape_string($conn, $_GET['doctors_referral_id']);

    // Query to fetch data from the database
    $query = "SELECT doctors_referral.*, staff.fname, staff.mname, staff.lname
	FROM doctors_referral
    	INNER JOIN staff ON doctors_referral.doctor_id = staff.staff_id
        INNER JOIN referral_records ON doctors_referral.rfrrl_id = referral_records.rfrrl_id
        WHERE referral_records.status != 'Accepted' AND doctors_referral.staff_id = $staff_id ORDER BY doctors_referral.id DESC";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = array();

        // Fetch data as associative array
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        // Return JSON response
        echo json_encode(['status' => 200, 'message' => 'Success', 'data' => $data]);
    } else {
        // Return JSON response for an error
        echo json_encode(['status' => 422, 'message' => 'Error in database query']);
    }

    // Close the database conn
    mysqli_close($conn);
}

if (isset($_GET['get_doctors_referral_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['get_doctors_referral_id']);

    $query = "SELECT * FROM doctors_referral WHERE id = '$id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $data = mysqli_fetch_assoc($query_run);
        $res = [
            'status' => 200,
            'message' => 'Patient found',
            'data' => $data
        ];
    } else {
        // At least one query failed
        $res = [
            'status' => 500,
            'message' => 'Patient not found'
        ];
    }

    echo json_encode($res);
}

if (isset($_POST['restore_referral'])) {
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rffrl_id']);

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("h:i A");

    $query = "UPDATE referral_records SET status = 'Pending' WHERE rfrrl_id='$rfrrl_id'";
    $query_run = mysqli_query($conn, $query);

    // Execute the second query
    $second_query = "DELETE FROM referral_transaction WHERE rfrrl_id = $rfrrl_id";
    $second_query_run = mysqli_query($conn, $second_query);

    $third_query = "DELETE FROM doctors_referral WHERE rfrrl_id = $rfrrl_id";
    $third_query_run = mysqli_query($conn, $third_query);

    if ($query_run && $second_query_run && $third_query) {
        // Both queries executed successfully
        $res = [
            'status' => 200,
            'message' => 'Both queries executed successfully'
        ];
        echo json_encode($res);
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