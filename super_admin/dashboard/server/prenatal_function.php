<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

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
    $sql = "CALL insert_first_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
    $sql = "CALL insert_second_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
    $sql = "CALL insert_third_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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

    // Create a prepared statement for the stored procedure
    $sql = "CALL update_third_trimester(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sissssssssssssss", $checkup, $patient_id, $date, $weight, $height, $age_of_gestation, $blood_pressure, $nutritional_status, $given_advise , $laboratory_tests_done,  $urinalysis, $complete_blood_count, $given_services, $date_of_return, $health_provider_name, $hospital_referral);

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

if (isset($_POST['add_patient'])) {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $contactNum = $_POST['contactNum'];
    $address = $_POST['address'];
    $fclt_id;

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_patient(?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sssssi", $fname, $mname, $lname, $contactNum, $address, $fclt_id);

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
    $edad = $_POST['edad'];
    $timbang = $_POST['timbang'];
    $taas = $_POST['taas'];
    $kalagayan_ng_kalusugan = $_POST['kalagayan_ng_kalusugan'];
    $petsa_ng_huling_regla = $_POST['petsa_ng_huling_regla'];
    $kailan_ako_manganganak = $_POST['kailan_ako_manganganak'];
    $pang_ilang_pagbubuntis = $_POST['pang_ilang_pagbubuntis'];

    // Create a prepared statement for the stored procedure
    $sql = "CALL insert_patients_details(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssssssii", $petsa_ng_unang_checkup, $edad, $timbang, $taas, $kalagayan_ng_kalusugan, $petsa_ng_huling_regla, $kailan_ako_manganganak, $pang_ilang_pagbubuntis, $patient_id, $records_count);

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
    $edad = $_POST['edad'];
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
    $stmt->bind_param("ssssssssii", $petsa_ng_unang_checkup, $edad, $timbang, $taas, $kalagayan_ng_kalusugan, $petsa_ng_huling_regla, $kailan_ako_manganganak, $pang_ilang_pagbubuntis, $patient_id, $records_count);

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

    $sql = "CALL insert_patient_record(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iiss", $patient_id, $fclt_id, $date, $time);

    if ($stmt->execute()) {
        $res = [
            'status' => 200,
            'message' => 'Patient added successfully',
            'patients id' => $patient_id
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

    $query = "DELETE FROM patients WHERE id='$patient_id'";
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
