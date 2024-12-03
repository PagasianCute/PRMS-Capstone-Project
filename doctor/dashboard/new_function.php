<?php
include_once '../../db/db_conn.php';
require_once '../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

if(isset($_POST['delete_field'])){
    $field_name = mysqli_real_escape_string($conn, $_POST['field_name']);

    $query = "DELETE FROM referral_format WHERE field_name='$field_name'";
    $query_run = mysqli_query($conn, $query);

    $second_query = "ALTER TABLE referral_forms DROP COLUMN $field_name";
    $second_query_run = mysqli_query($conn, $second_query);

    if($query_run && $second_query_run){
        
        $res = [
            'status' => 200,
            'message' => 'Field deleted successfully'
        ];
        echo json_encode($res);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'Field Deleted from ' . $field_name));
        return false;
    }else{
        $res = [
            'status' => 500,
            'message' => 'Field not deleted'
        ];
        echo json_encode($res);
        return false;
    }
}

if(isset($_POST['delete_prenatal_field'])){
    $field_name = mysqli_real_escape_string($conn, $_POST['field_name']);

    $query = "ALTER TABLE patients_details DROP COLUMN $field_name";
    $query_run = mysqli_query($conn, $query);

    if($query_run ){
        
        $res = [
            'status' => 200,
            'message' => 'Field deleted successfully'
        ];
        echo json_encode($res);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'Field Deleted from ' . $field_name));
        return false;
    }else{
        $res = [
            'status' => 500,
            'message' => 'Field not deleted'
        ];
        echo json_encode($res);
        return false;
    }
}

if(isset($_POST['delete_referral'])){
    $referral_id = mysqli_real_escape_string($conn, $_POST['referral_id']);

    $query = "DELETE FROM referral_forms WHERE id='$referral_id'";
    $query_run = mysqli_query($conn, $query);

    $query = "DELETE FROM referral_records WHERE rfrrl_id='$referral_id'";
    $second_query_run = mysqli_query($conn, $query);

    if($query_run && $second_query_run){
        
        $res = [
            'status' => 200,
            'message' => 'Field deleted successfully'
        ];
        echo json_encode($res);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'Referral Deleted from ' . $fclt_name));
        return false;
    }else{
        $res = [
            'status' => 500,
            'message' => 'Field not deleted'
        ];
        echo json_encode($res);
        return false;
    }
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


if (isset($_POST['patients_details'])) {
    $data = [];

    foreach ($_POST as $field => $value) {
        if ($field !== 'patients_details') { // Exclude the patient_id field
            $data[$field] = mysqli_real_escape_string($conn, $value);
        }
    }

    // Build the SQL query dynamically
    $columns = implode(', ', array_keys($data));
    $values = "'" . implode("', '", $data) . "'";
    $query = "INSERT INTO patients_details ($columns) VALUES ($values)";
    
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Patient added successfully'
        ];
        echo json_encode($res);
        return;
    } else {
        // Query execution failed
        $res = [
            'status' => 500,
            'message' => 'Patient not created successfully'
        ];
        echo json_encode($responseArray);
        return;
    }
}


if (isset($_POST['edited_patients_details'])) {
    $patientID = mysqli_real_escape_string($conn, $_POST['patients_id']);
    $data = [];

    // Loop through the posted data and sanitize it
    foreach ($_POST as $columnName => $value) {
        if ($columnName !== 'edited_patients_details' && $columnName !== 'patients_id') {
            $data[$columnName] = mysqli_real_escape_string($conn, $value);
        }
    }

    $setClauses = [];

    // Construct the SET clause for the SQL query
    foreach ($data as $columnName => $value) {
        $setClauses[] = "$columnName = '$value'";
    }

    $setClause = implode(', ', $setClauses);

    // Construct the SQL query for the UPDATE statement
    $query = "UPDATE patients_details 
              SET $setClause
              WHERE patients_id = '$patientID'";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Patient edited successfully'
        ];
        echo json_encode($res);
        return false;
    } else {
        // At least one query failed
        $res = [
            'status' => 500,
            'message' => 'Patient not edited successfully'
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

if (isset($_POST['save_field'])) {
    $field = mysqli_real_escape_string($conn, $_POST['field_name']);

    $field = str_replace(' ', '_', $field);

    if ($field == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Field is mandatory'
        ];
        echo json_encode($res);
        return false;
    }

    if (strpos($field, '/') !== false) {
        $res = [
            'status' => 300,
            'message' => 'Invalid Field Name'
        ];
        echo json_encode($res);
        return false;
    }

    if (!preg_match('/^\d+$/', $field)) {

        // Execute the second query
        $query = "ALTER TABLE referral_forms ADD $field varchar(255)";
        $query_run = mysqli_query($conn, $query);
        
    } else {
        $res = [
            'status' => 300,
            'message' => 'Invalid Field Name'
        ];
        echo json_encode($res);
        return false;
    }

    if ($query_run) {
        // Both queries executed successfully
        $res = [
            'status' => 200,
            'message' => 'Both queries executed successfully',
            'field_name' => $field
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

if (isset($_POST['send_message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $contact_id = mysqli_real_escape_string($conn, $_POST['contact_id']); // Get the contact ID

    if ($message == NULL) {
        $res = [
            'status' => 422,
            'message' => 'Field is mandatory'
        ];
        echo json_encode($res);
        return false;
    } else {
        date_default_timezone_set('Asia/Manila');
        $date = date("Y-m-d");
        $time = date("h:i:s A");

        // Use $contact_id in your SQL query
        $query = "INSERT INTO messages (user1, message, user2, date, time) VALUES ('$fclt_id', '$message', '$contact_id', '$date', '$time')";
        $query_run = mysqli_query($conn, $query);
    }

    if ($query_run) {
        $pusher->trigger('my-channel', 'my-event', array());
        $res = [
            'status' => 200,
            'message' => 'Message successfully sent'
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'Message failed'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_GET['contact_id'])) {
    $contactId = mysqli_real_escape_string($conn, $_GET['contact_id']);

    $responseData = [];

    // Modify your SQL query to include ORDER BY time
    $query = "SELECT * FROM messages WHERE (user1 = '$fclt_id' AND user2 = '$contactId') OR (user1 = '$contactId' AND user2 = '$fclt_id') ORDER BY date ASC, time ASC";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        while ($row = mysqli_fetch_assoc($query_run)) {
            // Add a 'type' property to indicate if it's a sent or received message
            $messageType = ($row['user1'] == $fclt_id) ? 'sent' : 'received';
            
            $responseData[] = [
                'message' => $row['message'],
                'type' => $messageType,
            ];
        }

        echo json_encode($responseData);
    } else {
        echo 'Query error: ' . mysqli_error($conn);
    }
}

if (isset($_GET['message_contact_id'])) {
    $contactId = mysqli_real_escape_string($conn, $_GET['message_contact_id']);

    $responseData = [];

    // Modify your SQL query to include ORDER BY time
    $query = "SELECT * FROM messages 
          WHERE (user1 = '$fclt_id' AND user2 = '$contactId') 
          OR (user1 = '$contactId' AND user2 = '$fclt_id') 
          ORDER BY date DESC, time DESC 
          LIMIT 1";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if ($query_run->num_rows > 0) {
            $row = $query_run->fetch_assoc();
            $latestMessage = $row["message"];
            $time = $row["time"]; // Get the time value
            
            // Create an array to hold the latest message and time
            $response = array(
                'latestMessage' => $latestMessage,
                'time' => $time
            );
            
            // Send the response as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }else {
        // Handle the case where there was an error in the query
        echo json_encode(array('error' => 'Error executing the query.'));
    }
    
    $conn->close();
}

if (isset($_FILES['profile_image'])) {
    $max_file_size = 2097152;

    // Check if file is not too large
    if ($_FILES['profile_image']['size'] > $max_file_size) {
        echo "File is too large. Maximum file size is 2MB.";
        exit;
    }

    // Check file type and extension
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($extension), $allowed_types)) {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        exit;
    }

    $upload_directory = __DIR__ . '/images/';

    if (!file_exists($upload_directory)) {
        // Create the directory if it doesn't exist
        mkdir($upload_directory, 0755, true);
    }

    $file_name = $_FILES['profile_image']['name'];
    $file_path = $upload_directory . $file_name;

    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
        echo "Failed to upload file.";
        exit;
    }

    // Insert file information into the database, including the file path
    // Ensure you have a valid database connection stored in $conn
    $query = "INSERT INTO profile_image (img_path) VALUES (?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $file_path);
        if ($stmt->execute()) {
            // File uploaded and information saved successfully, perform the redirect
            header("Location: index.php"); // Replace 'success.php' with the actual success page URL
            exit; // Make sure to exit after performing the redirect
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
