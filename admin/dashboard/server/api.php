<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

if (isset($_POST['create_referral'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $referred_hospital = mysqli_real_escape_string($conn, $_POST['referred_hospital']);

    if (empty($name) || empty($referred_hospital)) {
        $response = [
            'status' => 400,
            'message' => 'Name and Referred Hospital are required fields.',
        ];
        echo json_encode($response);
        exit;
    }

    $query = "INSERT INTO referral_forms (name, age) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $name, $age);
    $query_run = mysqli_stmt_execute($stmt);

    if ($query_run) {
        $new_inserted_id = mysqli_insert_id($conn);

        date_default_timezone_set('Asia/Manila');
        $date = date("Y-m-d");
        $time = date("h:i A");

        $query_another_table = "INSERT INTO referral_records (fclt_id, rfrrl_id, date, time, referred_hospital, status)
        VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt_another_table = mysqli_prepare($conn, $query_another_table);
        mysqli_stmt_bind_param($stmt_another_table, "iissi", $fclt_id, $new_inserted_id, $date, $time, $referred_hospital);
        $query_another_table_run = mysqli_stmt_execute($stmt_another_table);

        $notify_query = "INSERT INTO referral_notification (message, rfrrl_id, fclt_id, date, time, is_displayed)
        VALUES (?, ?, ?, ?, ?, '0')";
        $message = 'New Referral';
        $stmt_notify = mysqli_prepare($conn, $notify_query);
        mysqli_stmt_bind_param($stmt_notify, "siiss", $message, $new_inserted_id, $fclt_id, $date, $time);
        $notify_query_run = mysqli_stmt_execute($stmt_notify);

        if ($query_another_table_run && $notify_query_run) {
            $data = $fclt_name;
            $pusher->trigger($referred_hospital, 'referral', $data);
            $response = [
                'status' => 200,
                'message' => 'Referral data inserted successfully',
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Error inserting data into another table: ' . mysqli_error($conn),
            ];
        }
    } else {
        $response = [
            'status' => 422,
            'message' => 'Error inserting referral data: ' . mysqli_error($conn),
        ];
    }

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