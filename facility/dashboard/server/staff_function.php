<?php
session_start();
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';

$fclt_id = $_SESSION['fcltid'];
$uploadDirectory = '../assets/';


if (isset($_POST['add_staff'])) {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $contactNum = $_POST['contactNum'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $fclt_id;

    // Check if a file was uploaded
    if(isset($_FILES['staffformFile']) && $_FILES['staffformFile']['error'] == 0) {
        $uploadDir = '../assets/'; // Choose your upload directory
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
            $res = [
                'status' => 500,
                'message' => 'Error uploading file'
            ];
            echo json_encode($res);
            exit; // Stop further execution
        }
    } else {
        // No file was uploaded
        $uploadFile = null; // Set to a default value or handle as needed
    }

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_staff(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssssis", $fname, $mname, $lname, $contactNum, $address, $role, $fclt_id, $newFileName);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient added successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Patient not created successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
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