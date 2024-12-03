<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$fclt_type = $_SESSION["fclttype"];
$output = "";
$count = 0;

if (isset($_SESSION['fcltid'])) {
    $date_from = mysqli_real_escape_string($conn, $_POST['date-from']);
    $date_to = mysqli_real_escape_string($conn, $_POST['date-to']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if($_SESSION['fclttype'] == 'Birthing Home'){
    $sql = "SELECT * FROM referral_records INNER JOIN facilities ON referral_records.referred_hospital = facilities.fclt_id WHERE referral_records.date BETWEEN '$date_from' AND '$date_to'";
    } else {
        $sql = "SELECT referral_transaction.*, referral_records.fclt_id, facilities.fclt_name FROM referral_transaction INNER JOIN referral_records ON referral_transaction.rfrrl_id = referral_records.rfrrl_id
                INNER JOIN facilities ON referral_records.fclt_id = facilities.fclt_id WHERE referral_transaction.date BETWEEN '$date_from' AND '$date_to' AND referral_transaction.fclt_id = $fclt_id";
    }

    // Check if $status is not empty before adding it to the query
    if (!empty($status)) {
        if($_SESSION['fclttype'] == 'Birthing Home'){
        $sql .= " AND referral_records.status = '$status'";
        } else {
            $sql .= " AND referral_transaction.status = '$status'";
        }
    }

    $query = mysqli_query($conn, $sql);

    $resultCount = mysqli_num_rows($query); // Get the count of results

    if ($resultCount > 0) {
        $output .='<div class="result-container">
                        <div class="result-header">
                            <h4>Result (' . $resultCount . ' records)</h4>
                            <a class="btn btn-primary" id="print-btn" target="_blank" role="button"><i class="fi fi-sr-print"></i> Print</a>
                        </div>';
        $output .= '<table class="table table-hover" id="table">';
        if($_SESSION['fclttype'] == 'Birthing Home'){
            $output .= '<thead><tr><th>#</th><th>Referred Hospital</th><th>Referral ID</th><th>Date Created</th><th>Time Created</th><th>Status</th></tr></thead>';
            } else {
                $output .= '<thead><tr><th>#</th><th>Referring Facility</th><th>Referral ID</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>';
            }

        while ($row = mysqli_fetch_assoc($query)) {
            $count++;
            $output .= '<tr>';
            $output .= '<td>' . $count . '</td>';
            $output .= '<td>' . $row['fclt_name'] . '</td>';
            $output .= '<td>' . $row['rfrrl_id'] . '</td>';
            $output .= '<td>' . $row['date'] . '</td>';
            $output .= '<td>' . date('h:i A', strtotime($row['time'])) . '</td>';
            $output .= '<td>' . $row['status'] . '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';
        $output .='</div>';
    } else {
        $output .='<div class="result-container">';
        $output .= 'No records found';
        $output .='</div>';
    }

    echo $output;
} else {
    echo "User not authenticated";
}
?>
