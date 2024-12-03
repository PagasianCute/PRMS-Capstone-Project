<?php
session_start();
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';

if (isset($_POST['add_facility'])) {
    $fclt_name = $_POST['fclt_name'];
    $fclt_type = $_POST['fclt_type'];
    $fclt_contact = $_POST['fclt_contact'];
    $region_code = $_POST['region'];
    $region = $_POST['region-name'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $fclt_ref_id = $_POST['fclt_ref_id'];
    $verification = "Unverified";
    $status = "Offline";

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
    $sql = "CALL create_facility(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sisssssssss", $fclt_name, $fclt_ref_id, $fclt_type, $newFileName, $fclt_contact, $status, $verification, $region, $province, $municipality, $region_code);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Facility added successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Facility not created successfully',
            'error' => $error
        ];
        
        echo json_encode($res);
    }
    $stmt->close();
}

if (isset($_POST['update_facility'])) {
    $fclt_name = $_POST['fclt_name'];
    $fclt_type = $_POST['fclt_type'];
    $fclt_contact = $_POST['fclt_contact'];
    $region_code = $_POST['region'];
    $region = $_POST['region-name'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $fclt_ref_id = $_POST['fclt_ref_id'];
    $verification = "Unverified";
    $status = "Offline";

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
    $sql = "CALL create_facility(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sisssssssss", $fclt_name, $fclt_ref_id, $fclt_type, $newFileName, $fclt_contact, $status, $verification, $region, $province, $municipality, $region_code);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Facility added successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Facility not created successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }
    $stmt->close();
}

if (isset($_POST['facility_verification'])) {
    $fclt_id = $_POST['fclt_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $contact_num = $_POST['contact_num'];
    $birthdate = $_POST['birthdate'];
    $img = 'admin.jpg';
    $role = 'Admin';
    $verification = 'Verified';
    $status = "Offline";

    // Generate a random password
    $randomString = bin2hex(random_bytes(3));
    $password = substr($randomString, 0, 5);
    $pwd = $username . '.' . $birthdate . '.' . $password;
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    // Create a prepared statement for the stored procedure
    $sql = "CALL facility_verification(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("iddssssssssssss", $fclt_id, $latitude, $longitude, $fname, $mname, $lname, $username, $contact_num, $birthdate, $img, $role, $hashedPwd, $pwd, $verification, $status);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Facility verified successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Facility not verified successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }
    $stmt->close();
}


if (isset($_GET['view_fclt'])) {
    $fclt_id = filter_var($_GET['view_fclt'], FILTER_SANITIZE_NUMBER_INT);
    if (!$fclt_id || !is_numeric($fclt_id)) {
        echo json_encode([
            'status' => 400,
            'message' => 'Invalid facility ID provided'
        ]);
        exit;
    }

    try {
        // Call the stored procedure
        $query = "CALL get_facility(?)";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            throw new Exception('Failed to prepare the statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $fclt_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            throw new Exception('Error executing query: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) == 1) {
            $data = mysqli_fetch_assoc($result);
            $res = [
                'status' => 200,
                'message' => 'Data fetched successfully',
                'data' => $data
            ];
        } else {
            $res = [
                'status' => 404,
                'message' => 'Data not found'
            ];
        }

        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $res = [
            'status' => 500,
            'message' => 'Server error: ' . $e->getMessage()
        ];
    }

    echo json_encode($res);
    exit;
}

if (isset($_GET['remove_fclt'])) {
    $staff_id = $_GET['remove_fclt'];

    // Call the stored procedure
    $query = "CALL remove_facility(?)";
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