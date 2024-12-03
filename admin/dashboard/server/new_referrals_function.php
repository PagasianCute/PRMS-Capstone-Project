<?php
include_once '../../../db/db_conn.php';
include_once '../../../config/pusher.php';
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

if (isset($_GET['rffrl_id'])) {
    $rffrl_id = mysqli_real_escape_string($conn, $_GET['rffrl_id']);

    $query = "SELECT referral_forms.*, referral_records.*, facilities.fclt_name
        FROM referral_forms
        INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
        INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
        WHERE referral_forms.id = '$rffrl_id'";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $data = mysqli_fetch_assoc($query_run);

        $res = [
            'status' => 200,
            'message' => 'Data fetched successfully',
            'data' => $data,
        ];
    } else {
        $res = [
            'status' => 500,
            'message' => 'Error fetching data',
            'data' => null,
        ];
    }

    echo json_encode($res);
}