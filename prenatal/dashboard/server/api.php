<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

date_default_timezone_set('Asia/Manila');
$date = date("Y-m-d");
$time = date("h:i A");

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

    if (empty($name) || empty($referred_hospital)) {
        $response = [
            'status' => 400,
            'message' => 'Name and Referred Hospital are required fields.',
        ];
        echo json_encode($response);
        exit;
    }

    $sql = "CALL create_referral (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
    $stmt->bind_param("ssssssssssssssssssssssssssssssii", $name, $age, $sex, $bdate, $address, $admitting_dx, $rtpcr, $antigen, $clinical_ssx, $exposure_to_covid, $temp, $hr, $resp, $bp, $O2sat, $O2aided, $procedures_need, $fh, $ie, $fht, $lmp, $edc, $aog, $utz, $utz_aog, $edd, $enterpretation, $diagnostic_test, $time, $date, $fclt_id, $referred_hospital);

    // Execute the prepared statement
    $query_run = $stmt->execute();

    if ($query_run) {
        // Check the affected rows directly on the statement
        if ($stmt->affected_rows > 0) {
            $data = $fclt_name;  // Assuming $fclt_name is defined somewhere
            $pusher->trigger($referred_hospital, 'referral', $data);
            $response = [
                'status' => 200,
                'message' => 'Referral data inserted successfully',
            ];
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