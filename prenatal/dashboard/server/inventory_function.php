<?php
include_once '../../../db/db_conn.php';

session_start();
$users_id = $_SESSION["usersid"];

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$region = $_SESSION["fcltregion"];
$province = $_SESSION["fcltprovince"];
$municipality = $_SESSION["fcltmunicipality"];

if (isset($_POST['add_birth'])) {;
    $admission_date = $_POST['admission_date'];
    $admission_time = $_POST['admission_time'];
    $lname = $_POST['lname'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $birth_date = $_POST['birth_date'];
    $barangay = $_POST['barangay'];
    $orvida = $_POST['orvida'];
    $para = $_POST['para'];
    $age_of_gestation = $_POST['age_of_gestation'];
    $gender = $_POST['gender'];
    $head_circum = $_POST['head_circum'];
    $chest_circum = $_POST['chest_circum'];
    $length = $_POST['length'];
    $weigth = $_POST['weigth'];
    $discharge_date = $_POST['discharge_date'];
    $discharge_time = $_POST['discharge_time'];
    $birth_attendant = $_POST['birth_attendant'];


    // Create a prepared statement for the stored procedure
    $sql = "CALL add_birth(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sssssssssssssssssii", $admission_date, $admission_time, $lname, $fname, $mname, $birth_date, $barangay, $orvida, $para, $age_of_gestation, $gender, $head_circum, $chest_circum, $length, $weigth, $discharge_date, $discharge_time, $birth_attendant, $fclt_id);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Data added successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Data not added successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

if (isset($_POST['update_birth'])) {
    $case_number = $_POST['case_number'];
    $admission_date = $_POST['admission_date'];
    $admission_time = $_POST['admission_time'];
    $lname = $_POST['lname'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $birth_date = $_POST['birth_date'];
    $barangay = $_POST['barangay'];
    $orvida = $_POST['orvida'];
    $para = $_POST['para'];
    $age_of_gestation = $_POST['age_of_gestation'];
    $gender = $_POST['gender'];
    $head_circum = $_POST['head_circum'];
    $chest_circum = $_POST['chest_circum'];
    $length = $_POST['length'];
    $weigth = $_POST['weigth'];
    $discharge_date = $_POST['discharge_date'];
    $discharge_time = $_POST['discharge_time'];
    $birth_attendant = $_POST['birth_attendant'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_birth(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sssssssssssssssssii", $admission_date, $admission_time, $lname, $fname, $mname, $birth_date, $barangay, $orvida, $para, $age_of_gestation, $gender, $head_circum, $chest_circum, $length, $weigth, $discharge_date, $discharge_time, $birth_attendant, $case_number,);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Data updated successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Data not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

if(isset($_POST['delete_record'])){
    $case_number = mysqli_real_escape_string($conn, $_POST['case_number']);

    $query = "DELETE FROM booklet WHERE case_number='$case_number'";
    $query_run = mysqli_query($conn, $query);

    if($query_run){
        
        $res = [
            'status' => 200,
            'message' => 'Field deleted successfully'
        ];
        echo json_encode($res);
        //$pusher->trigger('my-channel', 'my-event', array('message' => 'Referral Deleted from ' . $fclt_name));
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


if (isset($_GET['view_record_id'])) {
    $view_record_id = $_GET['view_record_id'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT  booklet.*,
    staff.fname AS staff_fname, 
    staff.mname AS staff_mname, 
    staff.lname AS staff_lname FROM booklet INNER JOIN staff ON booklet.birth_attendant = staff.staff_id WHERE case_number = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for appointment_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $view_record_id);
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