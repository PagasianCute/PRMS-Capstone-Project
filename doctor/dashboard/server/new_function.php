<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$user_id = $_SESSION["usersid"];
$user_name = $_SESSION["usersname"];
$fclt_img = $_SESSION["fcltimg"];

function sendSMS($contact,$message) {
    $apiEndpoint = "https://app.philsms.com/api/v3/sms/send";
    $apiToken = "67|njtjcDHrgeWHk1iHxomWrMaw30FpX4iyyRjxt0WT"; // Replace with your actual API token

    $smsData = [
        'sender_id' => 'PhilSMS',
        'recipient' => $contact,
        'message' => $message,
    ];

    $jsonSMSData = json_encode($smsData);

    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonSMSData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Set headers
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $apiToken",
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute cURL session
    $smsResponse = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Return the PhilSMS API response
    return $smsResponse;
}

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

if (isset($_POST['accept_referral'])) {
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rffrl_id']);
    $fromFcltId = mysqli_real_escape_string($conn, $_POST['fclt_id']);
    $expectedTime = $_POST['expected_time'];
    $travelTime = $_POST['travel_time'];

    // Continue with the rest of your code for updating tables
    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("H:i");

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
    } else {
        $data = $fclt_name;
        $pusher->trigger($fromFcltId, 'referral_accept', $data);

        // Insert the SELECT statement here
        $select_sql = "SELECT staff_id FROM doctors_referral WHERE rfrrl_id = ? AND doctor_id = ?";
        $select_stmt = $conn->prepare($select_sql);

        if (!$select_stmt) {
            // Handle the error
            $response = [
                'status' => 500,
                'message' => 'Error preparing SELECT statement: ' . $conn->error,
            ];
            echo json_encode($response);
            exit;
        }

        // Binding parameters to the SELECT prepared statement
        $select_stmt->bind_param("ii", $rfrrl_id, $user_id);

        // Execute the SELECT prepared statement
        $select_query_run = $select_stmt->execute();

        if ($select_query_run) {
            // Fetch data from the SELECT result set
            $select_result = $select_stmt->get_result();
            $select_row = $select_result->fetch_assoc();

            // Use the data from the SELECT statement
            $selected_data = (string) $select_row['staff_id'];

            $pusher->trigger($selected_data, 'doctor_referral_accept', $user_name);

            // Close the SELECT result set
            $select_result->close();

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

                $message = "Your referral is accepted";
                $type = "Referral Accepted";

                $notification_sql = "CALL notification (?, ?, ?, ?, ?, ?, ?, ?)";
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
                $notification_stmt->bind_param("ssiisssi", $fclt_img, $message, $_SESSION['fcltid'], $new_fclt_id, $date, $time, $type, $user_id);
                // Execute the prepared statement
                $notification_query_run = $notification_stmt->execute();

                // SMS QUERY START
                $sms_query = "SELECT * FROM staff WHERE fclt_id = '$new_fclt_id' AND status = 'Active'";
                $sms_result = $conn->query($sms_query);
                
                if ($sms_result && $sms_result->num_rows > 0) {
                    while ($selected_data = $sms_result->fetch_assoc()) {
                        $contact_num = $selected_data['contact_num'];
                        $message = "Your referral is Accepted by " . $fclt_name .". Please bring the patient safely.";
                        sendSMS($contact_num, $message);
                    }
                
                    $response = [
                        'status' => 200,
                        'message' => 'Referral data inserted successfully, and SMS sent to active staff members',
                    ];
                } else {
                    // No active staff members found, send the message to all staff members
                    $sms_all_query = "SELECT * FROM staff WHERE fclt_id = '$new_fclt_id'";
                    $sms_result_all = $conn->query($sms_all_query);
                
                    if ($sms_result_all && $sms_result_all->num_rows > 0) {
                        while ($selected_data_all = $sms_result_all->fetch_assoc()) {
                            $contact_num_all = $selected_data_all['contact_num'];
                            $message_all = "Your referral is Accepted by " . $fclt_name .". Please bring the patient safely.";
                            sendSMS($contact_num_all, $message_all);
                        }
                
                        $response = [
                            'status' => 200,
                            'message' => 'Referral data inserted successfully, and SMS sent to all staff members',
                        ];
                    } else {
                        $response = [
                            'status' => 500,
                            'message' => 'No staff members found or error executing SELECT statement: ' . $conn->error,
                        ];
                    }
                }
                // SMS QUERY END

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
        $select_stmt->close();
    }

    // Insert into referral_transaction using extracted data
    $second_query = "INSERT INTO referral_transaction (fclt_id, rfrrl_id, status, attendant, date, time, reason, arrival, travel_time, expected_arrival) VALUES (?, ?, 'Accepted', ?, ?, ?, 'NULL', 'Arriving', ?, ?)";
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

    mysqli_stmt_bind_param($second_query_stmt, "ssissss", $fclt_id, $rfrrl_id, $user_id, $date, $time, $travelTime, $expectedTime);
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

    if ($query_run && $second_query_run) {
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
    $time = date("H:i");

    $query = "UPDATE referral_records SET status ='Declined' WHERE rfrrl_id='$rfrrl_id'";
    $query_run = mysqli_query($conn, $query);

    // Execute the second query
    $second_query = "INSERT INTO referral_transaction (fclt_id, rfrrl_id, status, attendant, date, time, reason) VALUES ('$fclt_id', '$rfrrl_id', 'Declined', '$user_id', '$date', '$time', '$reason')"; // Use $reason here
    $second_query_run = mysqli_query($conn, $second_query);

    if ($query_run && $second_query_run) {

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

            $message = $reason;
            $type = "Referral Declined";

            $notification_sql = "CALL notification (?, ?, ?, ?, ?, ?, ?, ?)";
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
            $notification_stmt->bind_param("ssiisssi", $fclt_img, $message, $_SESSION['fcltid'], $new_fclt_id, $date, $time, $type, $user_id);
            // Execute the prepared statement
            $notification_query_run = $notification_stmt->execute();
            $data = array(
                'reason' => $reason,
                'fclt_name' => $fclt_name,
            );
            $pusher->trigger($selected_data,'referral_declined',$data);

            // SMS QUERY START
            $sms_query = "SELECT * FROM staff WHERE fclt_id = '$new_fclt_id' AND status = 'Active'";
            $sms_result = $conn->query($sms_query);
            
            if ($sms_result && $sms_result->num_rows > 0) {
                while ($selected_data = $sms_result->fetch_assoc()) {
                    $contact_num = $selected_data['contact_num'];
                    $message = "Your referral is Declined by " . $fclt_name ." for a reason. Transfering your referral to Caraga Regional Hospital...";
                    sendSMS($contact_num, $message);
                }
            
                $response = [
                    'status' => 200,
                    'message' => 'Referral data inserted successfully, and SMS sent to active staff members',
                ];
            } else {
                // No active staff members found, send the message to all staff members
                $sms_all_query = "SELECT * FROM staff WHERE fclt_id = '$new_fclt_id'";
                $sms_result_all = $conn->query($sms_all_query);
            
                if ($sms_result_all && $sms_result_all->num_rows > 0) {
                    while ($selected_data_all = $sms_result_all->fetch_assoc()) {
                        $contact_num_all = $selected_data_all['contact_num'];
                        $message_all = "Your referral is Declined by " . $fclt_name ." for a reason. Transfering your referral to Caraga Regional Hospital...";
                        sendSMS($contact_num_all, $message_all);
                    }
            
                    $response = [
                        'status' => 200,
                        'message' => 'Referral data inserted successfully, and SMS sent to all staff members',
                    ];
                } else {
                    $response = [
                        'status' => 500,
                        'message' => 'No staff members found or error executing SELECT statement: ' . $conn->error,
                    ];
                }
            }
            // SMS QUERY END

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
            'message' => 'Both queries executed successfully',
            'fclt_id' => $new_fclt_id
        ];
        echo json_encode($res);
        //$pusher->trigger('my-channel', 'my-event', array('message' => 'Referral declined by: ' . $fclt_name));
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


    if ($query_run && $second_query_run) {
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
    $query = "UPDATE referral_transaction SET arrival = 'Arrived', patient_status_upon_arrival = ?, receiving_oEfficer = ?, arrival_date = ?, arrival_time = ? WHERE rfrrl_id = ? AND fclt_id = ?";
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

    mysqli_stmt_bind_param($stmt, "ssssss", $patient_status_upon_arrival, $receiving_officer, $arrival_date, $arrival_time, $rfrrl_id, $fclt_id);
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
    $second_query = "INSERT INTO referral_notification (message, rfrrl_id, fclt_id, date, time) VALUES ('Patient Arrived', ?, ?, ?, ?)";
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