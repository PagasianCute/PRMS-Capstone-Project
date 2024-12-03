<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

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

// Create a response array
$response = [
    'status' => 200,  // Set the default status
    'message' => 'Operation successful',  // Set the default message
];

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$user_name = $_SESSION["usersname"];
$fclt_img = $_SESSION["fcltimg"];
$user_id = $_SESSION["usersid"];

date_default_timezone_set('Asia/Manila');
$date = date("Y-m-d");
$time = date("H:i");

if (isset($_POST['create_referral'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $bdate = mysqli_real_escape_string($conn, $_POST['bdate']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $admitting_dx = mysqli_real_escape_string($conn, $_POST['admitting_dx']);
    $rtpcr = mysqli_real_escape_string($conn, $_POST['rtpcr']);
    $antigen = mysqli_real_escape_string($conn, $_POST['antigen']);
    $clinical_ssx = mysqli_real_escape_string($conn, $_POST['clinical_ssx']);
    $exposure_to_covid = mysqli_real_escape_string($conn, $_POST['exposure_to_covid']);
    $temp = mysqli_real_escape_string($conn, $_POST['temp']);
    $hr = mysqli_real_escape_string($conn, $_POST['hr']);
    $resp = mysqli_real_escape_string($conn, $_POST['resp']);
    $bp = mysqli_real_escape_string($conn, $_POST['bp']);
    $O2sat = mysqli_real_escape_string($conn, $_POST['O2sat']);
    $O2aided = mysqli_real_escape_string($conn, $_POST['O2aided']);
    $procedures_need = mysqli_real_escape_string($conn, $_POST['procedures_need']);
    $fh = mysqli_real_escape_string($conn, $_POST['fh']);
    $ie = mysqli_real_escape_string($conn, $_POST['ie']);
    $fht = mysqli_real_escape_string($conn, $_POST['fht']);
    $lmp = mysqli_real_escape_string($conn, $_POST['lmp']);
    $edc = mysqli_real_escape_string($conn, $_POST['edc']);
    $aog = mysqli_real_escape_string($conn, $_POST['aog']);
    $utz = mysqli_real_escape_string($conn, $_POST['utz']);
    $utz_aog = mysqli_real_escape_string($conn, $_POST['utz_aog']);
    $edd = mysqli_real_escape_string($conn, $_POST['edd']);
    $enterpretation = mysqli_real_escape_string($conn, $_POST['enterpretation']);
    $diagnostic_test = mysqli_real_escape_string($conn, $_POST['diagnostic_test']);
    $referred_hospital = mysqli_real_escape_string($conn, $_POST['referred_hospital']);
    $patients_id = mysqli_real_escape_string($conn, $_POST['referral-patient-id']);
    $referral_reason = mysqli_real_escape_string($conn, $_POST['referral_reason']);
    $emergency_type = mysqli_real_escape_string($conn, $_POST['emergency_type']);

    if ($referral_reason == "Other"){
        $referral_reason = mysqli_real_escape_string($conn, $_POST['other']);
    }else{
        $referral_reason = mysqli_real_escape_string($conn, $_POST['referral_reason']);
    }

    if (empty($name) || empty($referred_hospital)) {
        $response = [
            'status' => 400,
            'message' => 'Name and Referred Hospital are required fields.',
        ];
        echo json_encode($response);
        exit;
    }

    $sql = "CALL create_referral (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $response = [
            'status' => 500,
            'message' => 'Error preparing statement: ' . $conn->error,
        ];
        echo json_encode($response);
        exit;
    }

    // Binding parameters to the prepared statement
    $stmt->bind_param("ssssssssssssssssssssssssssssssiiisssi", $name, $age, $sex, $bdate, $address, $admitting_dx, $rtpcr, $antigen, $clinical_ssx, $exposure_to_covid, $temp, $hr, $resp, $bp, $O2sat, $O2aided, $procedures_need, $fh, $ie, $fht, $lmp, $edc, $aog, $utz, $utz_aog, $edd, $enterpretation, $diagnostic_test, $time, $date, $fclt_id, $referred_hospital,$patients_id,$referral_reason, $fclt_img, $emergency_type, $user_id);

    // Execute the prepared statement
    $query_run = $stmt->execute();

    if ($query_run) {
        // Check the affected rows directly on the statement
        if ($stmt->affected_rows > 0) {
            $data = array(
                'fclt_name' => $fclt_name,
                'emergency_type' => $emergency_type,
            );
            $pusher->trigger($referred_hospital, 'referral', $data);
            $response = [
                'status' => 200,
                'message' => 'Referral data inserted successfully',
            ];

            // SMS QUERY START
            $sms_query = "SELECT * FROM staff WHERE fclt_id = '$referred_hospital' AND status = 'Active'";
            $sms_result = $conn->query($sms_query);
            
            if ($sms_result && $sms_result->num_rows > 0) {
                while ($selected_data = $sms_result->fetch_assoc()) {
                    $contact_num = $selected_data['contact_num'];
                    $message = "New referral from " . $fclt_name .". Please check the portal";
                    sendSMS($contact_num, $message);
                }
            
                $response = [
                    'status' => 200,
                    'message' => 'Referral data inserted successfully, and SMS sent to active staff members',
                ];
            } else {
                // No active staff members found, send the message to all staff members
                $sms_all_query = "SELECT * FROM staff WHERE fclt_id = '$referred_hospital'";
                $sms_result_all = $conn->query($sms_all_query);
            
                if ($sms_result_all && $sms_result_all->num_rows > 0) {
                    while ($selected_data_all = $sms_result_all->fetch_assoc()) {
                        $contact_num_all = $selected_data_all['contact_num'];
                        $message_all = "New referral. Please check the portal";
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

        } else {
            $response = [
                'status' => 500,
                'message' => 'No rows affected, or error in inserting data into another table: ' . $stmt->error,
            ];
        }
    } else {
        $response = [
            'status' => 500,
            'message' => 'Error executing statement: ' . $stmt->error,
        ];
    }

    // Close the prepared statement
    $stmt->close();

    echo json_encode($response);
}

if (isset($_POST['create_referral_new'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $bdate = mysqli_real_escape_string($conn, $_POST['bdate']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $admitting_dx = mysqli_real_escape_string($conn, $_POST['admitting_dx']);
    $rtpcr = mysqli_real_escape_string($conn, $_POST['rtpcr']);
    $antigen = mysqli_real_escape_string($conn, $_POST['antigen']);
    $clinical_ssx = mysqli_real_escape_string($conn, $_POST['clinical_ssx']);
    $exposure_to_covid = mysqli_real_escape_string($conn, $_POST['exposure_to_covid']);
    $temp = mysqli_real_escape_string($conn, $_POST['temp']);
    $hr = mysqli_real_escape_string($conn, $_POST['hr']);
    $resp = mysqli_real_escape_string($conn, $_POST['resp']);
    $bp = mysqli_real_escape_string($conn, $_POST['bp']);
    $O2sat = mysqli_real_escape_string($conn, $_POST['O2sat']);
    $O2aided = mysqli_real_escape_string($conn, $_POST['O2aided']);
    $procedures_need = mysqli_real_escape_string($conn, $_POST['procedures_need']);
    $fh = mysqli_real_escape_string($conn, $_POST['fh']);
    $ie = mysqli_real_escape_string($conn, $_POST['ie']);
    $fht = mysqli_real_escape_string($conn, $_POST['fht']);
    $lmp = mysqli_real_escape_string($conn, $_POST['lmp']);
    $edc = mysqli_real_escape_string($conn, $_POST['edc']);
    $aog = mysqli_real_escape_string($conn, $_POST['aog']);
    $utz = mysqli_real_escape_string($conn, $_POST['utz']);
    $utz_aog = mysqli_real_escape_string($conn, $_POST['utz_aog']);
    $edd = mysqli_real_escape_string($conn, $_POST['edd']);
    $enterpretation = mysqli_real_escape_string($conn, $_POST['enterpretation']);
    $diagnostic_test = mysqli_real_escape_string($conn, $_POST['diagnostic_test']);
    $referred_hospital = mysqli_real_escape_string($conn, $_POST['referred_hospital']);
    $patients_id = mysqli_real_escape_string($conn, $_POST['referral-patient-id']);
    $referral_reason = mysqli_real_escape_string($conn, $_POST['referral_reason']);
    $emergency_type = mysqli_real_escape_string($conn, $_POST['emergency_type']);
    $referral_patient_record = mysqli_real_escape_string($conn, $_POST['referral-patient-record']);

    if ($referral_reason == "Other"){
        $referral_reason = mysqli_real_escape_string($conn, $_POST['other']);
    }else{
        $referral_reason = mysqli_real_escape_string($conn, $_POST['referral_reason']);
    }

    if (empty($name) || empty($referred_hospital)) {
        $response = [
            'status' => 400,
            'message' => 'Name and Referred Hospital are required fields.',
        ];
        echo json_encode($response);
        exit;
    }

    $sql = "CALL create_referral (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $response = [
            'status' => 500,
            'message' => 'Error preparing statement: ' . $conn->error,
        ];
        echo json_encode($response);
        exit;
    }

    // Binding parameters to the prepared statement
    $stmt->bind_param("ssssssssssssssssssssssssssssssiiisssi", $name, $age, $sex, $bdate, $address, $admitting_dx, $rtpcr, $antigen, $clinical_ssx, $exposure_to_covid, $temp, $hr, $resp, $bp, $O2sat, $O2aided, $procedures_need, $fh, $ie, $fht, $lmp, $edc, $aog, $utz, $utz_aog, $edd, $enterpretation, $diagnostic_test, $time, $date, $fclt_id, $referred_hospital,$patients_id,$referral_reason, $fclt_img, $emergency_type, $user_id);

    // Execute the prepared statement
    $query_run = $stmt->execute();

    if ($query_run) {
        // Check the affected rows directly on the statement
        if ($stmt->affected_rows > 0) {
            $data = array(
                'fclt_name' => $fclt_name,
                'emergency_type' => $emergency_type,
            );
            $pusher->trigger($referred_hospital, 'referral', $data);

            $new_status = 1;
            $update_sql = "UPDATE for_referral_patients SET status = ? WHERE patients_id = ? AND record = ?";
            $update_stmt = $conn->prepare($update_sql);

            // Bind parameters for the first update statement
            $update_stmt->bind_param("iii", $new_status, $patients_id, $referral_patient_record);

            // Execute the first update statement
            $update_query_run = $update_stmt->execute();
            if ($update_query_run) {
    
                $response = [
                    'status' => 200,
                    'message' => 'Referral data inserted successfully, and additional data updated in another table',
                ];
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Error updating data in another table: ' . $update_stmt->error,
                ];
            }
    
            // Close the first update statement
            $update_stmt->close();
        } else {
            $response = [
                'status' => 500,
                'message' => 'No rows affected, or error in inserting data into another table: ' . $stmt->error,
            ];
        }
    } else {
        $response = [
            'status' => 500,
            'message' => 'Error executing statement: ' . $stmt->error,
        ];
    }
    $stmt->close();

    echo json_encode($response);
}

if (isset($_GET['myrecord_rffrl_id'])) {
    $rffrl_id = mysqli_real_escape_string($conn, $_GET['myrecord_rffrl_id']);

    $query = "SELECT referral_forms.*, referral_records.*, facilities.*
    FROM referral_forms
    INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital
    WHERE referral_forms.id = '$rffrl_id'";
    $query_run = mysqli_query($conn, $query);

    $querytransactions = "SELECT *
	FROM referral_transaction
    INNER JOIN facilities ON referral_transaction.fclt_id = facilities.fclt_id
    WHERE rfrrl_id = '$rffrl_id'";
    $querytransactions_run = mysqli_query($conn, $querytransactions);

    $queryData = mysqli_fetch_array($query_run);

    $querytransactions_run = mysqli_query($conn, $querytransactions);

    $querytransactions_data = [];
    while ($row = mysqli_fetch_array($querytransactions_run)) {
        $querytransactions_data[] = $row;
    }

    $res = [
        'status' => 200,
        'message' => 'Data fetched successfully',
        'data' => $queryData,
        'transactions' => $querytransactions_data,
    ];

    echo json_encode($res);
}

if (isset($_POST['submit_referral'])) {
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $rfrrl_id = mysqli_real_escape_string($conn, $_POST['rfrrl_id']);

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("H:i");

    $sql = "CALL submit_referral_to_doctor (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $response = [
            'status' => 500,
            'message' => 'Error preparing statement: ' . $conn->error,
        ];
        echo json_encode($response);
        exit;
    }

    // Binding parameters to the prepared statement
    $stmt->bind_param("iiissi", $doctor_id, $rfrrl_id, $staff_id, $date, $time, $fclt_id);

    // Execute the prepared statement
    $query_run = $stmt->execute();

    if ($query_run) {
        // Check the affected rows directly on the statement
        if ($stmt->affected_rows > 0) {
            // Your referral was submitted successfully

            // Add another SQL query for updating the second table
            $update_sql = "UPDATE referral_records SET status = ? WHERE rfrrl_id = ?";
            $update_stmt = $conn->prepare($update_sql);

            if (!$update_stmt) {
                // Handle the error
                $response = [
                    'status' => 500,
                    'message' => 'Error preparing update statement: ' . $conn->error,
                ];
                echo json_encode($response);
                exit;
            }

            // Set the values for the update statement
            $new_value = "Sent To a Doctor";  // Change this to the new value you want to set

            // Binding parameters to the update prepared statement
            $update_stmt->bind_param("si", $new_value,  $rfrrl_id);

            // Execute the update prepared statement
            $update_query_run = $update_stmt->execute();

            if ($update_query_run && $update_stmt->affected_rows > 0) {
                
                $select_sql = "SELECT fclt_id FROM referral_records WHERE rfrrl_id = ?";
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
                $select_stmt->bind_param("i", $rfrrl_id);

                // Execute the SELECT prepared statement
                $select_query_run = $select_stmt->execute();

                if ($select_query_run) {
                    // Fetch data from the SELECT result set
                    $select_result = $select_stmt->get_result();
                    $select_row = $select_result->fetch_assoc();

                    // Use the data from the SELECT statement
                    $selected_data = (string) $select_row['fclt_id'];
                    $new_fclt_id = $select_row['fclt_id'];

                    $message = "Your referral is on the process";
                    $type = "Referral Process";

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

                    $data = $fclt_name;
                    $pusher->trigger($selected_data,'referral_process',$data);

                    $pusher->trigger($doctor_id,'referral_sent',$user_name);

                    // Close the SELECT result set
                    $select_result->close();

                    // SMS QUERY START
                    $sms_query = "SELECT * FROM staff WHERE staff_id = '$doctor_id' AND status = 'Active'";
                    $sms_result = $conn->query($sms_query);
                    
                    if ($sms_result && $sms_result->num_rows > 0) {
                        while ($selected_data = $sms_result->fetch_assoc()) {
                            $contact_num = $selected_data['contact_num'];
                            $message = "New referral sent by " . $user_name .". Please check the portal";
                            sendSMS($contact_num, $message);
                        }
                    
                        $response = [
                            'status' => 200,
                            'message' => 'Referral data inserted successfully, and SMS sent to active staff members',
                        ];
                    } else {
                        // No active staff members found, send the message to all staff members
                        $sms_all_query = "SELECT * FROM staff WHERE fclt_id = '$fclt_id' AND role = 'Doctor'";
                        $sms_result_all = $conn->query($sms_all_query);
                    
                        if ($sms_result_all && $sms_result_all->num_rows > 0) {
                            while ($selected_data_all = $sms_result_all->fetch_assoc()) {
                                $contact_num_all = $selected_data_all['contact_num'];
                                $message_all = "New referral. Please check the portal";
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

                } else {
                    // Handle the error executing the SELECT statement
                    $response = [
                        'status' => 500,
                        'message' => 'Error executing SELECT statement: ' . $select_stmt->error,
                    ];
                    echo json_encode($response);
                    exit;
                }

                // Close the SELECT prepared statement
                $select_stmt->close();

                // Continue with the rest of your code using the $selected_data variable
                // ...

                $response = [
                    'status' => 200,
                    'message' => 'Referral submitted successfully, second table updated, and SELECT statement executed',
                    'selected_data' => $selected_data,
                ];

            } else {
                // Handle the case where the second table update failed
                $response = [
                    'status' => 500,
                    'message' => 'Error updating second table: ' . $update_stmt->error,
                ];
            }

            // Close the update prepared statement
            $update_stmt->close();

        } else {
            // Handle the case where no rows were affected or an error occurred in the referral submission
            $response = [
                'status' => 500,
                'message' => 'No rows affected, or error in referral submission: ' . $stmt->error,
            ];
        }
    } else {
        // Handle the error executing the referral submission statement
        $response = [
            'status' => 500,
            'message' => 'Error executing referral submission statement: ' . $stmt->error,
        ];
    }

    // Close the prepared statement
    $stmt->close();

    echo json_encode($response);
}

if (isset($_POST['edit_submit_referral'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $doctors_referral_id = mysqli_real_escape_string($conn, $_POST['doctors_referral_id']);

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("H:i");

    $query = "UPDATE doctors_referral SET doctor_id = '$doctor_id', sent_date = '$date', sent_time = '$time' WHERE id ='$doctors_referral_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
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