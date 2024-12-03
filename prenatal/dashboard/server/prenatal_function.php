<?php
include_once '../../../db/db_conn.php';

session_start();
date_default_timezone_set('Asia/Manila');
$users_id = $_SESSION["usersid"];

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$region = $_SESSION["fcltregion"];
$province = $_SESSION["fcltprovince"];
$municipality = $_SESSION["fcltmunicipality"];

if (isset($_GET['trimester_table'])) {
    $trimester = $_GET['trimester_table'];
    $patient_id = $_GET['patientid'];
    $checkup = $_GET['check_up'];
    $records_count = $_GET['records_count'];

    // Use prepared statements to prevent SQL injection
    $countQuery = "SELECT COUNT(*) AS record_count FROM prenatal_records WHERE patients_id = ?";
    $countStmt = mysqli_prepare($conn, $countQuery);    
    
    // Assuming 'i' is the correct type for patients_id; adjust if needed
    mysqli_stmt_bind_param($countStmt, "i", $patient_id);
    mysqli_stmt_execute($countStmt);

    $countResult = mysqli_stmt_get_result($countStmt);

    // Check for errors in the query execution
    if (!$countResult) {
        die('Error in count query: ' . mysqli_error($conn));
    }

    $countData = mysqli_fetch_assoc($countResult);
    $currentCount = $countData['record_count'];

    // Close the count query
    mysqli_stmt_close($countStmt);

    
    if($records_count == ''){
        $currentCount = $countData['record_count'];
    }else{
        $currentCount = $_GET['records_count'];
    }

    $dataQuery = "
        SELECT *
        FROM prenatal_records pr
        INNER JOIN $trimester tt ON tt.patients_id = pr.patients_id
        WHERE tt.patients_id = ? AND tt.check_up = ? AND tt.records_count = $currentCount
        ORDER BY tt.records_count DESC
        LIMIT 1";

    $dataStmt = mysqli_prepare($conn, $dataQuery);

    // Assuming 'is' is the correct type for patients_id and check_up; adjust if needed
    mysqli_stmt_bind_param($dataStmt, "is", $patient_id, $checkup);
    mysqli_stmt_execute($dataStmt);

    $dataResult = mysqli_stmt_get_result($dataStmt);

    // Check for errors in the data query execution
    if (!$dataResult) {
        die('Error in data query: ' . mysqli_error($conn));
    }

    // Check the number of rows
    if (mysqli_num_rows($dataResult) == 1) {
        $data = mysqli_fetch_array($dataResult);

        $res = [
            'status' => 200,
            'message' => 'Data fetchedasdaddas',
            'data' => $data,
            'table' => $trimester,
            'checkup' => $checkup,
            'record_count' => $currentCount
        ];
    } else {
        $res = [
            'status' => 404,
            'message' => 'Data not found'
        ];
    }

    // Close the data query
    mysqli_stmt_close($dataStmt);

    echo json_encode($res);
}


if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];
    $records_count = $_GET['records_count'];

    // Call the stored procedure
    $query = "CALL get_birth_experience(?,?)";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patient_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "ii", $patient_id, $records_count);
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

if (isset($_GET['patient_details_id'])) {
    $patient_id = $_GET['patient_details_id'];
    $records_count = $_GET['records_count'];

    // Call the stored procedure
    $query = "CALL get_patients_details(?,?)";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patient_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "ii", $patient_id, $records_count);
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
            'message' => 'Data not found',
            'record' => $records_count
        ];
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);

    echo json_encode($res);
}

if (isset($_POST['first_trimesters_insert'])) {
    $checkup = $_POST['checkup'];
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date = $_POST['firstTri_date'];
    $weight = $_POST['firstTri_weight'];
    $height = $_POST['firstTri_height'];
    $age_of_gestation = $_POST['firstTri_age_of_gestation'];
    $blood_pressure = $_POST['firstTri_blood_pressure'];
    $nutritional_status = $_POST['firstTri_nutritional_status'];
    $laboratory_tests_done = $_POST['firstTri_laboratory_tests_done'];
    $hemoglobin_count = $_POST['firstTri_hemoglobin_count'];
    $urinalysis = $_POST['firstTri_urinalysis'];
    $complete_blood_count = $_POST['firstTri_complete_blood_count'];
    $stis_using_a_syndromic_approach = $_POST['firstTri_stis_using_a_syndromic_approach'];
    $tetanus_containing_vaccine = $_POST['firstTri_tetanus_containing_vaccine'];
    $given_services = $_POST['firstTri_given_services'];
    $date_of_return = $_POST['firstTri_date_of_return'];
    $health_provider_name = $_POST['firstTri_health_provider_name'];
    $hospital_referral = $_POST['firstTri_hospital_referral'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_first_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssssssiii", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $laboratory_tests_done, $hemoglobin_count, $urinalysis, $complete_blood_count, $stis_using_a_syndromic_approach, $tetanus_containing_vaccine, $given_services, $date_of_return, $health_provider_name, $hospital_referral, $records_count, $users_id, $fclt_id);

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

if (isset($_POST['first_trimesters_update'])) {
    $checkup = $_POST['checkup'];
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date = $_POST['firstTri_date'];
    $weight = $_POST['firstTri_weight'];
    $height = $_POST['firstTri_height'];
    $age_of_gestation = $_POST['firstTri_age_of_gestation'];
    $blood_pressure = $_POST['firstTri_blood_pressure'];
    $nutritional_status = $_POST['firstTri_nutritional_status'];
    $laboratory_tests_done = $_POST['firstTri_laboratory_tests_done'];
    $hemoglobin_count = $_POST['firstTri_hemoglobin_count'];
    $urinalysis = $_POST['firstTri_urinalysis'];
    $complete_blood_count = $_POST['firstTri_complete_blood_count'];
    $stis_using_a_syndromic_approach = $_POST['firstTri_stis_using_a_syndromic_approach'];
    $tetanus_containing_vaccine = $_POST['firstTri_tetanus_containing_vaccine'];
    $given_services = $_POST['firstTri_given_services'];
    $date_of_return = $_POST['firstTri_date_of_return'];
    $health_provider_name = $_POST['firstTri_health_provider_name'];
    $hospital_referral = $_POST['firstTri_hospital_referral'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_first_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssssssi", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $laboratory_tests_done, $hemoglobin_count, $urinalysis, $complete_blood_count, $stis_using_a_syndromic_approach, $tetanus_containing_vaccine, $given_services, $date_of_return, $health_provider_name, $hospital_referral, $records_count);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient updated successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Patient not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

if (isset($_POST['second_trimesters_insert'])) {
    $checkup = $_POST['checkup'];
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date = $_POST['secondTri_date'];
    $weight = $_POST['secondTri_weight'];
    $height = $_POST['secondTri_height'];
    $age_of_gestation = $_POST['secondTri_age_of_gestation'];
    $blood_pressure = $_POST['secondTri_blood_pressure'];
    $nutritional_status = $_POST['secondTri_nutritional_status'];
    $given_advise = $_POST['secondTri_given_advise'];
    $laboratory_tests_done = $_POST['secondTri_laboratory_tests_done'];
    $urinalysis = $_POST['secondTri_urinalysis'];
    $complete_blood_count = $_POST['secondTri_complete_blood_count'];
    $given_services = $_POST['secondTri_given_services'];
    $date_of_return = $_POST['secondTri_date_of_return'];
    $health_provider_name = $_POST['secondTri_health_provider_name'];
    $hospital_referral = $_POST['secondTri_hospital_referral'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_second_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssssii", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $given_advise , $laboratory_tests_done,  $urinalysis, $complete_blood_count, $given_services, $date_of_return, $health_provider_name, $hospital_referral, $records_count, $users_id);

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

if (isset($_POST['second_trimesters_update'])) {
    $checkup = $_POST['checkup'];
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date = $_POST['secondTri_date'];
    $weight = $_POST['secondTri_weight'];
    $height = $_POST['secondTri_height'];
    $age_of_gestation = $_POST['secondTri_age_of_gestation'];
    $blood_pressure = $_POST['secondTri_blood_pressure'];
    $nutritional_status = $_POST['secondTri_nutritional_status'];
    $given_advise = $_POST['secondTri_given_advise'];
    $laboratory_tests_done = $_POST['secondTri_laboratory_tests_done'];
    $urinalysis = $_POST['secondTri_urinalysis'];
    $complete_blood_count = $_POST['secondTri_complete_blood_count'];
    $given_services = $_POST['secondTri_given_services'];
    $date_of_return = $_POST['secondTri_date_of_return'];
    $health_provider_name = $_POST['secondTri_health_provider_name'];
    $hospital_referral = $_POST['secondTri_hospital_referral'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_second_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssssi", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $given_advise , $laboratory_tests_done,  $urinalysis, $complete_blood_count, $given_services, $date_of_return, $health_provider_name, $hospital_referral, $records_count);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient updated successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Patient not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

if (isset($_POST['third_trimesters_insert'])) {
    $checkup = $_POST['checkup'];
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date = $_POST['thirdTri_date'];
    $weight = $_POST['thirdTri_weight'];
    $height = $_POST['thirdTri_height'];
    $age_of_gestation = $_POST['thirdTri_age_of_gestation'];
    $blood_pressure = $_POST['thirdTri_blood_pressure'];
    $nutritional_status = $_POST['thirdTri_nutritional_status'];
    $given_advise = $_POST['thirdTri_given_advise'];
    $laboratory_tests_done = $_POST['thirdTri_laboratory_tests_done'];
    $urinalysis = $_POST['thirdTri_urinalysis'];
    $complete_blood_count = $_POST['thirdTri_complete_blood_count'];
    $given_services = $_POST['thirdTri_given_services'];
    $date_of_return = $_POST['thirdTri_date_of_return'];
    $health_provider_name = $_POST['thirdTri_health_provider_name'];
    $hospital_referral = $_POST['thirdTri_hospital_referral'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_third_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssssii", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $given_advise , $laboratory_tests_done,  $urinalysis, $complete_blood_count, $given_services, $date_of_return, $health_provider_name, $hospital_referral, $records_count, $users_id);

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

if (isset($_POST['third_trimesters_update'])) {
    $checkup = $_POST['checkup'];
    $patient_id = $_POST['patient_id'];
    $date = $_POST['thirdTri_date'];
    $weight = $_POST['thirdTri_weight'];
    $height = $_POST['thirdTri_height'];
    $age_of_gestation = $_POST['thirdTri_age_of_gestation'];
    $blood_pressure = $_POST['thirdTri_blood_pressure'];
    $nutritional_status = $_POST['thirdTri_nutritional_status'];
    $given_advise = $_POST['thirdTri_given_advise'];
    $laboratory_tests_done = $_POST['thirdTri_laboratory_tests_done'];
    $urinalysis = $_POST['thirdTri_urinalysis'];
    $complete_blood_count = $_POST['thirdTri_complete_blood_count'];
    $given_services = $_POST['thirdTri_given_services'];
    $date_of_return = $_POST['thirdTri_date_of_return'];
    $health_provider_name = $_POST['thirdTri_health_provider_name'];
    $hospital_referral = $_POST['thirdTri_hospital_referral'];
    $records_count = $_POST['selected_row'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_third_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssssi", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $given_advise , $laboratory_tests_done,  $urinalysis, $complete_blood_count, $given_services, $date_of_return, $health_provider_name, $hospital_referral, $records_count);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient updated successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Patient not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

if (isset($_POST['birth_experience_insert'])) {
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date_of_delivery = $_POST['date_of_delivery'];
    $type_of_delivery = $_POST['type_of_delivery'];
    $birth_outcome = $_POST['birth_outcome'];
    $number_of_children_delivered = $_POST['number_of_children_delivered'];
    $pregnancy_hypertension = $_POST['pregnancy_hypertension'];
    $preeclampsia_eclampsia = $_POST['preeclampsia_eclampsia'];
    $bleeding_during_pregnancy = $_POST['bleeding_during_pregnancy'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_birth_experience(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("isssssssi", $patient_id, $date_of_delivery, $type_of_delivery, $birth_outcome, $number_of_children_delivered, $pregnancy_hypertension, $preeclampsia_eclampsia, $bleeding_during_pregnancy, $records_count);

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

if (isset($_POST['birth_experience_update'])) {
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $date_of_delivery = $_POST['date_of_delivery'];
    $type_of_delivery = $_POST['type_of_delivery'];
    $birth_outcome = $_POST['birth_outcome'];
    $number_of_children_delivered = $_POST['number_of_children_delivered'];
    $pregnancy_hypertension = $_POST['pregnancy_hypertension'];
    $preeclampsia_eclampsia = $_POST['preeclampsia_eclampsia'];
    $bleeding_during_pregnancy = $_POST['bleeding_during_pregnancy'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_birth_experience(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("isssssssi", $patient_id, $date_of_delivery, $type_of_delivery, $birth_outcome, $number_of_children_delivered, $pregnancy_hypertension, $preeclampsia_eclampsia, $bleeding_during_pregnancy, $records_count);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient updated successfully',
            'record' => $records_count,
            'patient_id' => $patient_id
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Patient not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

function calculateAge($birthdate) {
    $dob = new DateTime($birthdate);
    $currentDate = new DateTime();
    $age = $currentDate->diff($dob)->y;
    return $age;
}

if (isset($_POST['add_patient'])) {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $contactNum = $_POST['contactNum'];
    $barangay = $_POST['barangay'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $age = calculateAge($birthdate);
    $fclt_id;
    $region = $_SESSION["fcltregion"];
    $province = $_SESSION["fcltprovince"];
    $municipality = $_SESSION["fcltmunicipality"];

    date_default_timezone_set('Asia/Manila');
    $date_registered = date("Y-m-d");

    // Create a prepared statement for the stored procedure,
    $sql = "CALL insert_patient(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssisssssssisi", $fname, $mname, $lname, $gender, $age, $birthdate, $contactNum, $region, $province, $municipality, $barangay, $email, $fclt_id, $date_registered, $users_id);

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

if (isset($_POST['patients_details_insert'])) {
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $petsa_ng_unang_checkup = $_POST['petsa_ng_unang_checkup'];
    $timbang = $_POST['timbang'];
    $taas = $_POST['taas'];
    $kalagayan_ng_kalusugan = $_POST['kalagayan_ng_kalusugan'];
    $petsa_ng_huling_regla = $_POST['petsa_ng_huling_regla'];
    $kailan_ako_manganganak = $_POST['kailan_ako_manganganak'];
    $pang_ilang_pagbubuntis = $_POST['pang_ilang_pagbubuntis'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_patients_details(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sssssssii", $petsa_ng_unang_checkup, $timbang, $taas, $kalagayan_ng_kalusugan, $petsa_ng_huling_regla, $kailan_ako_manganganak, $pang_ilang_pagbubuntis, $patient_id, $records_count);

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

if (isset($_POST['patients_details_update'])) {
    $patient_id = $_POST['patient_id'];
    $records_count = $_POST['selected_row'];
    $petsa_ng_unang_checkup = $_POST['petsa_ng_unang_checkup'];
    $timbang = $_POST['timbang'];
    $taas = $_POST['taas'];
    $kalagayan_ng_kalusugan = $_POST['kalagayan_ng_kalusugan'];
    $petsa_ng_huling_regla = $_POST['petsa_ng_huling_regla'];
    $kailan_ako_manganganak = $_POST['kailan_ako_manganganak'];
    $pang_ilang_pagbubuntis = $_POST['pang_ilang_pagbubuntis'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_patients_details(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssssssii", $petsa_ng_unang_checkup, $timbang, $taas, $kalagayan_ng_kalusugan, $petsa_ng_huling_regla, $kailan_ako_manganganak, $pang_ilang_pagbubuntis, $patient_id, $records_count);

    // Execute the statement
    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient updated successfully'
        ];
        echo json_encode($res);
    } else {
        $error = $stmt->error;
        $res = [
            'status' => 500,
            'message' => 'Patient not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}

if (isset($_GET['view_patient_records_id'])) {
    $patients_id = $_GET['view_patient_records_id'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM patients WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patients_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $patients_id);
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

if (isset($_GET['get_patient_records'])) {
    $patients_id = mysqli_real_escape_string($conn, $_GET['get_patient_records']);
    $output = "";

    $sql = "SELECT * FROM prenatal_records WHERE patients_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "s", $patients_id);

    // Execute the query
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $output .=  '<div class="col">' .
                        '<div class="alert alert-primary d-flex records_alert" role="alert">' .
                        '<h6>Record ' . $row['records_count'] . ' • ' . $row['date'] . '</h6>' .
                        '<a class="btn btn-primary" href="view_prenatal.php?id=' . $row['patients_id'] . '&record=' . $row['records_count'] . '" role="button">View</a>' .
                        '</div>' .
                        '</div>';
        }
        echo $output;
    } else {
        $output .= '<div class="alert alert-primary d-flex records_alert" role="alert">' .
                    '<h6>No Records</h6>' .
                    '</div>';
        echo $output;
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}


if (isset($_GET['view_patient_id'])) {
    $patients_id = $_GET['view_patient_id'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM patients WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patients_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $patients_id);
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

if (isset($_GET['patient_count_id'])) {
    $patients_id = $_GET['patient_count_id'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM prenatal_records WHERE patients_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patients_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $patients_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Check for errors in the query execution
    if (!$result) {
        die('Error in query: ' . mysqli_error($conn));
    }

    // Check the number of rows
    if (mysqli_num_rows($result) > 0) {
        $data = array();

        while ($row = mysqli_fetch_assoc($result)) {
            // Append each row to the $data array
            $data[] = $row;
        }

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

if (isset($_GET['record_count_id'])) {
    $patients_id = $_GET['record_count_id'];
    $record_count = $_GET['record'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM prenatal_records WHERE patients_id = ? AND records_count = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for patients_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "ii", $patients_id, $record_count);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Check for errors in the query execution
    if (!$result) {
        die('Error in query: ' . mysqli_error($conn));
    }

    // Check the number of rows
    if (mysqli_num_rows($result) > 0) {
        $data = array();

        while ($row = mysqli_fetch_assoc($result)) {
            // Append each row to the $data array
            $data[] = $row;
        }

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


if (isset($_POST['new_record'])) {
    $patient_id = $_POST['patients_id'];
    
    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("H:i:s");

    // Debugging logs
    error_log("Attempting to insert new patient record...");
    error_log("Patient ID: $patient_id, Date: $date, Time: $time");

    // Insert new record
    $sqlInsert = "CALL insert_patient_record(?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);

    if ($stmtInsert === false) {
        error_log("Prepare failed for insert_patient_record: " . $conn->error);
        die(json_encode([
            'status' => 500,
            'message' => 'Database error: Unable to prepare insert statement.'
        ]));
    }

    $stmtInsert->bind_param("iissi", $patient_id, $fclt_id, $date, $time, $users_id);

    if ($stmtInsert->execute()) {
        error_log("Insert executed successfully.");

        // Count the number of rows
        $sqlCount = "SELECT COUNT(*) AS record_count FROM prenatal_records WHERE patients_id = ?";
        $stmtCount = $conn->prepare($sqlCount);

        if ($stmtCount === false) {
            error_log("Prepare failed for record count: " . $conn->error);
            die(json_encode([
                'status' => 500,
                'message' => 'Database error: Unable to prepare count statement.'
            ]));
        }

        $stmtCount->bind_param("i", $patient_id);
        if ($stmtCount->execute()) {
            $stmtCount->bind_result($record_count);
            $stmtCount->fetch();
            $stmtCount->close();

            error_log("Record count fetched successfully: $record_count");

            $res = [
                'status' => 200,
                'message' => 'Patient added successfully',
                'patients_id' => $patient_id,
                'record_count' => $record_count
            ];

            echo json_encode($res);
        } else {
            error_log("Execution failed for record count: " . $stmtCount->error);
            die(json_encode([
                'status' => 500,
                'message' => 'Database error: Unable to fetch record count.'
            ]));
        }
    } else {
        error_log("Execution failed for insert_patient_record: " . $stmtInsert->error);
        
        $res = [
            'status' => 500,
            'message' => 'Patient not created successfully. Please try again later.'. $stmtInsert->error
        ];

        echo json_encode($res);
    }

    $stmtInsert->close();
}

if (isset($_POST['patients_ids'])) {
    $patient_id = $_POST['patients_ids'];
    
    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $sql = "CALL insert_patient_record(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iiss", $patient_id, $fclt_id, $date, $time);

    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient added successfullyppp',
        ];
        echo json_encode($res);
    } else {

        error_log("Execution failed: " . $stmt->error);
        
        $res = [
            'status' => 500,
            'message' => 'Patient not created successfully. Please try again later.'
        ];
        echo json_encode($res);
    }

    $stmt->close();
}

if (isset($_POST['create_referral'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $referred_hospital = $_POST['referred_hospital'];

    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");
    $time = date("H:i:s");

    // Create a prepared statement for the stored procedure
    $sql = "CALL create_referral(?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssii", $name, $age, $date, $time, $referred_hospital, $fclt_id);

    // Execute the statement
    if ($stmt->execute()) {
        $pusher->trigger('my-channel', 'my-event', array('message' => 'New Referral from ' . $fclt_name));
        $res = [
            'status' => 200,
            'message' => 'Referral created successfully'
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

if(isset($_POST['delete_patient'])){
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);

    // First delete statement
    $query1 = "DELETE FROM patients WHERE id='$patient_id'";
    $query_run1 = mysqli_query($conn, $query1);

    // Second delete statement
    $query2 = "DELETE FROM prenatal_records WHERE patients_id='$patient_id'";
    $query_run2 = mysqli_query($conn, $query2);

    if($query_run1 && $query_run2){
        $res = [
            'status' => 200,
            'message' => 'Fields deleted successfully'
        ];
        echo json_encode($res);
        return false;
    }else{
        $res = [
            'status' => 500,
            'message' => 'Fields not deleted'
        ];
        echo json_encode($res);
        return false;
    }
}


if (isset($_POST['delete_record'])) {
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $record = mysqli_real_escape_string($conn, $_POST['record']);

    // Start the transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete the row from prenatal_records table
        $delete_query_prenatal = "DELETE FROM prenatal_records WHERE patients_id='$patient_id' AND records_count='$record'";
        $delete_query_run_prenatal = mysqli_query($conn, $delete_query_prenatal);

        if (!$delete_query_run_prenatal) {
            throw new Exception("Error deleting record from prenatal_records");
        }

        // Update the records_count for the remaining rows in prenatal_records
        $update_query_prenatal = "UPDATE prenatal_records 
                                  SET records_count = records_count - 1 
                                  WHERE patients_id = '$patient_id' AND records_count > '$record'";
        $update_query_run_prenatal = mysqli_query($conn, $update_query_prenatal);

        if (!$update_query_run_prenatal) {
            throw new Exception("Error updating records_count in prenatal_records");
        }

        // Delete and update operations for table2 (replace 'table2' with your actual table name)
        $delete_query_patients_details = "DELETE FROM patients_details WHERE patients_id='$patient_id' AND records_count='$record'";
        $delete_query_run_patients_details = mysqli_query($conn, $delete_query_patients_details);

        if (!$delete_query_run_patients_details) {
            throw new Exception("Error deleting record from patients_details");
        }

        $update_query_patients_details = "UPDATE patients_details 
                               SET records_count = records_count - 1 
                               WHERE patients_id = '$patient_id' AND records_count > '$record'";
        $update_query_run_patients_details = mysqli_query($conn, $update_query_patients_details);

        if (!$update_query_run_patients_details) {
            throw new Exception("Error updating records_count in patients_details");
        }

        // Delete and update operations for table3 (replace 'table3' with your actual table name)
        $delete_query_birth_experience = "DELETE FROM birth_experience WHERE patients_id='$patient_id' AND records_count='$record'";
        $delete_query_run_birth_experience = mysqli_query($conn, $delete_query_birth_experience);

        if (!$delete_query_run_birth_experience) {
            throw new Exception("Error deleting record from birth_experience");
        }

        $update_query_birth_experience = "UPDATE birth_experience 
                               SET records_count = records_count - 1 
                               WHERE patients_id = '$patient_id' AND records_count > '$record'";
        $update_query_run_birth_experience = mysqli_query($conn, $update_query_birth_experience);

        if (!$update_query_run_birth_experience) {
            throw new Exception("Error updating records_count in birth_experience");
        }

        // Delete and update operations for table3 (replace 'table3' with your actual table name)
        $delete_query_first_trimester = "DELETE FROM first_trimester WHERE patients_id='$patient_id' AND records_count='$record'";
        $delete_query_run_first_trimester = mysqli_query($conn, $delete_query_first_trimester);

        if (!$delete_query_run_first_trimester) {
            throw new Exception("Error deleting record from first_trimester");
        }

        $update_query_first_trimester = "UPDATE first_trimester 
                               SET records_count = records_count - 1 
                               WHERE patients_id = '$patient_id' AND records_count > '$record'";
        $update_query_run_first_trimester = mysqli_query($conn, $update_query_first_trimester);

        if (!$update_query_run_first_trimester) {
            throw new Exception("Error updating records_count in first_trimester");
        }

        // Delete and update operations for table3 (replace 'table3' with your actual table name)
        $delete_query_second_trimester = "DELETE FROM second_trimester WHERE patients_id='$patient_id' AND records_count='$record'";
        $delete_query_run_second_trimester = mysqli_query($conn, $delete_query_second_trimester);

        if (!$delete_query_run_second_trimester) {
            throw new Exception("Error deleting record from second_trimester");
        }

        $update_query_second_trimester = "UPDATE second_trimester 
                               SET records_count = records_count - 1 
                               WHERE patients_id = '$patient_id' AND records_count > '$record'";
        $update_query_run_second_trimester = mysqli_query($conn, $update_query_second_trimester);

        if (!$update_query_run_second_trimester) {
            throw new Exception("Error updating records_count in second_trimester");
        }

        // Delete and update operations for table3 (replace 'table3' with your actual table name)
        $delete_query_third_trimester = "DELETE FROM third_trimester WHERE patients_id='$patient_id' AND records_count='$record'";
        $delete_query_run_third_trimester = mysqli_query($conn, $delete_query_third_trimester);

        if (!$delete_query_run_third_trimester) {
            throw new Exception("Error deleting record from third_trimester");
        }

        $update_query_third_trimester = "UPDATE third_trimester 
                               SET records_count = records_count - 1 
                               WHERE patients_id = '$patient_id' AND records_count > '$record'";
        $update_query_run_third_trimester = mysqli_query($conn, $update_query_third_trimester);

        if (!$update_query_run_third_trimester) {
            throw new Exception("Error updating records_count in third_trimester");
        }

        // Delete and update operations for table3 (replace 'table3' with your actual table name)
        $delete_query_patient_schedule = "DELETE FROM patient_schedule WHERE patients_id='$patient_id' AND record='$record'";
        $delete_query_run_patient_schedule = mysqli_query($conn, $delete_query_patient_schedule);

        if (!$delete_query_run_patient_schedule) {
            throw new Exception("Error deleting record from patient_schedule");
        }

        $update_query_patient_schedule = "UPDATE patient_schedule 
                               SET record = record - 1 
                               WHERE patients_id = '$patient_id' AND record > '$record'";
        $update_query_run_patient_schedule = mysqli_query($conn, $update_query_patient_schedule);

        if (!$update_query_run_patient_schedule) {
            throw new Exception("Error updating record in patient_schedule");
        }

        // Commit the transaction
        mysqli_commit($conn);

        $res = [
            'status' => 200,
            'message' => 'Field deleted successfully'
        ];
        echo json_encode($res);
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        mysqli_rollback($conn);

        $res = [
            'status' => 500,
            'message' => $e->getMessage()
        ];
        echo json_encode($res);
    }
}

if (isset($_GET['view_appointment_id'])) {
    $appointment_id = $_GET['view_appointment_id'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM patient_schedule INNER JOIN patients ON patient_schedule.patients_id = patients.id WHERE schedule_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Assuming 'i' is the correct type for appointment_id; adjust if needed
    mysqli_stmt_bind_param($stmt, "i", $appointment_id);
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

if (isset($_POST['update_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $patients_id = $_POST['patients_id'];
    $trimesters = $_POST['trimesters'];
    $checkup = $_POST['checkup'];
    $date_of_return = $_POST['date_of_return'];
    $record = $_POST['record'];

    $sql_patient_schedule = "UPDATE patient_schedule SET date = ? WHERE patients_id = ? AND schedule_id = ? AND record = ?";

    $stmt_patient_schedule = $conn->prepare($sql_patient_schedule);

    if ($stmt_patient_schedule === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt_patient_schedule->bind_param("siii", $date_of_return,  $patients_id, $appointment_id, $record);

    if ($stmt_patient_schedule->execute()) {
        
        if (strpos($trimesters, 'First Trimester') !== false){
            $trimester = "first_trimester";
        } else if (strpos($trimesters, 'Second Trimester') !== false){
            $trimester = "second_trimester";
        } else if (strpos($trimesters, 'Third Trimester') !== false){
            $trimester = "second_trimester";
        }
        
        if (strpos($checkup, 'First Checkup') !== false){
            $checkup = "third_checkup";
        } else if (strpos($checkup, 'Second Checkup') !== false){
            $checkup = "first_checkup";
        } else if (strpos($checkup, 'Third Checkup') !== false){
            $checkup = "second_checkup";
        }

        $sql_another_table = "UPDATE $trimester SET date_of_return = ? WHERE patients_id = ? AND check_up = ? AND records_count = ?";

        $stmt_another_table = $conn->prepare($sql_another_table);

        if ($stmt_another_table === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt_another_table->bind_param("sisi", $date_of_return, $patients_id, $checkup, $record);

        if ($stmt_another_table->execute()) {
            $res = [
                'status' => 200,
                'message' => 'Appointment and Another Table updated successfully'
            ];
            echo json_encode($res);
        } else {
            $error = $stmt_another_table->error;
            $res = [
                'status' => 500,
                'message' => 'Another Table not updated successfully',
                'error' => $error
            ];
            echo json_encode($res);
        }

        $stmt_another_table->close();
    } else {
        // If update for "patient_schedule" fails
        $error = $stmt_patient_schedule->error;
        $res = [
            'status' => 500,
            'message' => 'Appointment and Another Table not updated successfully',
            'error' => $error
        ];
        echo json_encode($res);
    }

    $stmt_patient_schedule->close();
    $conn->close();
}

if (isset($_GET['checkTrimester'])) {
    $patients_id = $_GET['patients_id'];
    $record = $_GET['records_count'];
    $check_checkup = "first_checkup";

    $res = [];  // Initialize an array to store results

    // Check for the first trimester
    $query1 = "SELECT COUNT(*) AS count FROM first_trimester WHERE patients_id = ? AND records_count = ?";
    $stmt1 = mysqli_prepare($conn, $query1);
    mysqli_stmt_bind_param($stmt1, "ii", $patients_id, $record);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);

    if (!$result1) {
        die('Error in query: ' . mysqli_error($conn));
    }

    $data1 = mysqli_fetch_array($result1);
    $count1 = $data1['count'];

    if ($count1 > 0) {
        $res['first_trimester'] = [
            'status' => 100,
            'message' => 'First trimester has data',
            'count' => $count1
        ];
    } else {
        $res['first_trimester'] = [
            'status' => 404,
            'message' => 'First trimester has no data'
        ];
    }

    mysqli_stmt_close($stmt1);

    // Check for the second trimester
    $query2 = "SELECT COUNT(*) AS count FROM second_trimester WHERE patients_id = ? AND records_count = ?";
    $stmt2 = mysqli_prepare($conn, $query2);
    mysqli_stmt_bind_param($stmt2, "ii", $patients_id, $record);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);

    if (!$result2) {
        die('Error in query: ' . mysqli_error($conn));
    }

    $data2 = mysqli_fetch_array($result2);
    $count2 = $data2['count'];

    if ($count2 > 0) {
        $res['second_trimester'] = [
            'status' => 200,
            'message' => 'Second trimester has data',
            'count' => $count2
        ];
    } else {
        $res['second_trimester'] = [
            'status' => 405,
            'message' => 'Second trimester has no data'
        ];
    }

    mysqli_stmt_close($stmt2);

    echo json_encode($res);
}

if (isset($_GET['check_Checkup'])) {
    $trimester_table = $_GET['new_trimester_table'];
    $patientid = $_GET['new_patientid'];
    $records_count = $_GET['new_records_count'];
    $check_up1 = "first_checkup";
    $check_up2 = "second_checkup";
    $currentDate = date("Y-m-d");

    $res = [];  // Initialize an array to store results

    // Check for the first trimester
    $query1 = "SELECT * FROM $trimester_table WHERE patients_id = ? AND records_count = ? AND check_up = ?";
    $stmt1 = mysqli_prepare($conn, $query1);
    mysqli_stmt_bind_param($stmt1, "iis", $patientid, $records_count, $check_up1);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);

    if (!$result1) {
        die('Error in query: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($result1) == 1) {
        $data1 = mysqli_fetch_array($result1);
        $dateOfReturn1 = $data1['date_of_return'];

        // Check if the date_of_return has passed
        if ($dateOfReturn1 <= $currentDate) {
            $res['first_trimester'] = [
                'status' => 100,
                'message' => 'First checkup passed',
                'data' => $data1
            ];
        } else {
            $res['first_trimester'] = [
                'status' => 404,
                'message' => 'First checkup not yet passed'
            ];
        }
    } else {
        $res['first_trimester'] = [
            'status' => 404,
            'message' => 'First checkup not found'
        ];
    }

    mysqli_stmt_close($stmt1);

    // Check for the second trimester
    $query2 = "SELECT * FROM $trimester_table WHERE patients_id = ? AND records_count = ? AND check_up = ?";
    $stmt2 = mysqli_prepare($conn, $query2);
    mysqli_stmt_bind_param($stmt2, "iis", $patientid, $records_count, $check_up2);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);

    if (!$result2) {
        die('Error in query: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($result2) == 1) {
        $data2 = mysqli_fetch_array($result2);
        $dateOfReturn2 = $data2['date_of_return'];

        // Check if the date_of_return has passed
        if ($dateOfReturn2 <= $currentDate) {
            $res['second_trimester'] = [
                'status' => 200,
                'message' => 'Second checkup passed',
                'data' => $data2
            ];
        } else {
            $res['second_trimester'] = [
                'status' => 405,
                'message' => 'Second checkup not yet passed'
            ];
        }
    } else {
        $res['second_trimester'] = [
            'status' => 405,
            'message' => 'Second checkup not found'
        ];
    }

    mysqli_stmt_close($stmt2);

    echo json_encode($res);
}

if(isset($_POST['update_schedule'])){
    $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
    $status = 'Declined';

    $query = "UPDATE patient_schedule SET status = '$status' WHERE schedule_id = '$schedule_id'";
    $query_run = mysqli_query($conn, $query);

    if($query_run){
        $res = [
            'status' => 200,
            'message' => 'Field updated successfully'
        ];
        echo json_encode($res);
        //$pusher->trigger('my-channel', 'my-event', array('message' => 'Referral Updated for ' . $fclt_name));
        return false;
    }else{
        $res = [
            'status' => 500,
            'message' => 'Field not updated'
        ];
        echo json_encode($res);
        return false;
    }
}