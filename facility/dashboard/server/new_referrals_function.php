<?php
include_once '../../../db/db_conn.php';
require_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$users_id = $_SESSION["usersid"];
$fclt_img = $_SESSION["fcltimg"];

if (isset($_GET['rffrl_id'])) {
    $rffrl_id = mysqli_real_escape_string($conn, $_GET['rffrl_id']);

    $query = "SELECT referral_forms.*, referral_records.*, facilities.*, staff.*
    FROM referral_forms
    INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
    INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
    LEFT JOIN staff ON referral_records.staff_id = staff.staff_id
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