<?php
session_start();
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';

function sendSMS($recipient, $message) {
    $apiEndpoint = "https://app.philsms.com/api/v3/sms/send";
    $apiToken = "67|njtjcDHrgeWHk1iHxomWrMaw30FpX4iyyRjxt0WT"; // Replace with your actual API token

    $smsData = [
        'sender_id' => 'PhilSMS',
        'recipient' => $recipient,
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

// Initialize $fclt_id from the session
$fclt_id = $_SESSION['fcltid'];

if (isset($_POST['add_staff'])) {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $contactNum = $_POST['contactNum'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $birth_date = $_POST['birth_date'];
    $username = $_POST['username'];
    $status = 'Offline';

    // Generate a random password
    $randomString = bin2hex(random_bytes(3));
    $password = substr($randomString, 0, 5);
    $pwd = $username . '.' . $birth_date . '.' . $password;
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    // Check if a file was uploaded
    if (isset($_FILES['staffformFile']) && $_FILES['staffformFile']['error'] == 0) {
        $uploadDir = '../../../assets/'; // Choose your upload directory
        $originalFileName = basename($_FILES['staffformFile']['name']);

        // Generate a random string (you can use any suitable method)
        $randomString = bin2hex(random_bytes(8));

        // Combine the random string and original file name
        $newFileName = $randomString . '_' . $originalFileName;
        $uploadFile = $uploadDir . $newFileName;

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['staffformFile']['tmp_name'], $uploadFile)) {
            // File has been successfully uploaded
            $fileName = basename($_FILES['staffformFile']['name']); // Extract just the file name
        } else {
            // Handle file upload error
            $response['status'] = 500;
            $response['message'] = 'Error uploading file';
            echo json_encode($response);
            exit; // Stop further execution
        }
    } else {
        // No file was uploaded
        $uploadFile = null; // Set to a default value or handle as needed
    }

    // Check if the username already exists
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM staff WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();

    // If the count is greater than 0, the username already exists
    if ($count > 0) {
        $response['status'] = 404;
        $response['message'] = 'Username Already Exists';
        echo json_encode($response);
        $check_stmt->close();
        $conn->close();
        exit(); // You can handle this situation as needed, for example, redirecting the user or showing an error message.
    }

    $check_stmt->close();

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_staff(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssssissssss", $fname, $mname, $lname, $contactNum, $address, $role, $fclt_id, $newFileName, $birth_date, $username, $hashedPwd, $pwd, $status);

    // Execute the statement
    if ($stmt->execute()) {
        // Staff added successfully
        $response['status'] = 200;
        $response['message'] = 'Staff added successfully';

        // Send SMS with the password to the staff's phone number
        $smsRecipient = $contactNum; // Assuming $contactNum contains the staff's phone number from the database
        $smsMessage = "Your password is: $pwd";

        // Call the function to send SMS
        $smsResponse = sendSMS($smsRecipient, $smsMessage);

        // Add SMS response to the main response for debugging
        $response['sms_response'] = $smsResponse;

        // Continue with the rest of your response handling
        // ...
    } else {
        // Staff not created successfully
        $error = $stmt->error;
        $response['status'] = 500;
        $response['message'] = 'Staff not created successfully';
        $response['error'] = $error;
    }

    // Close the statement
    $stmt->close();
    
// Output the JSON response
echo json_encode($response);
}



if (isset($_GET['view_staff'])) {
    $staff_id = $_GET['view_staff'];

    // Call the stored procedure
    $query = "CALL get_staff(?)";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patient_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $staff_id,);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Check for errors in the query execution
    if (!$result) {
        die('Error in query: ' . mysqli_error($conn));
    }

    // Check the number of rows
    if (mysqli_num_rows($result) == 1) {
        $data = mysqli_fetch_array($result);

        $res = [
            'status' => 200,
            'message' => 'Data fetched',
            'data' => $data
        ];
    } else {
        $res = [
            'status' => 404,
            'message' => 'Data not found'
        ];
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);

    echo json_encode($res);
}

if (isset($_GET['delete_staff'])) {
    $staff_id = $_GET['delete_staff'];

    // Call the stored procedure
    $query = "CALL remove_staff(?)";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for staff_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $staff_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    // Check for errors in the query execution
    if (!$stmt) {
        die('Error in query: ' . mysqli_error($conn));
    }

    // Check the number of affected rows
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $res = [
            'status' => 200,
            'message' => 'Staff Removed'
        ];
    } else {
        $res = [
            'status' => 404,
            'message' => 'Data not found'
        ];
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);

    echo json_encode($res);
}