<?php
include_once '../../../db/db_conn.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$fclt_id = $_SESSION['fcltid'];

if (isset($_POST['searchTerm'])) {
    $searchTerm = $_POST['searchTerm'];
    $fclt_id = $_SESSION['fcltid'];

    // Perform the query to fetch filtered rows from the "referral_forms" table
    $sql = "SELECT referral_transaction.*,facilities.*,staff.lname,staff.fname,staff.mname,referral_forms.name
            FROM referral_records
            INNER JOIN referral_transaction ON referral_transaction.rfrrl_id = referral_records.rfrrl_id
            INNER JOIN referral_forms ON referral_forms.id = referral_transaction.rfrrl_id
            INNER JOIN facilities ON facilities.fclt_id = referral_records.fclt_id
            LEFT JOIN staff ON referral_transaction.receiving_officer = staff.staff_id
            WHERE referral_transaction.fclt_id = '$fclt_id'
            AND (facilities.fclt_name LIKE '%$searchTerm%'
                OR referral_forms.name LIKE '%$searchTerm%'
                OR referral_transaction.status LIKE '%$searchTerm%'
                OR referral_transaction.arrival LIKE '%$searchTerm%'
                OR staff.fname LIKE '%$searchTerm%'
                OR staff.mname LIKE '%$searchTerm%'
                OR staff.lname LIKE '%$searchTerm%')
            ORDER BY referral_transaction.id DESC";

    // If the search term is empty, limit the number of rows fetched
    if (empty($searchTerm)) {
        $sql .= " LIMIT 12"; // You can adjust the limit as needed
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch all rows into an associative array
        $referrals = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free the result set
        mysqli_free_result($result);
        $count = 0;
        // Output the filtered rows
        foreach ($referrals as $data) {
            $count++;

            if(empty($data['lname']) && empty($data['fname']) && empty($data['mname'])){
                $birthAttendantFullName = "";
            }else{
                $birthAttendantFullName = $data['lname'] . ', ' . $data['fname'] . ' ' . $data['mname'];
            }
            echo "<tr>";
            echo "<th scope='row'>{$count}</th>";
            echo "<td>{$data['fclt_name']}</td>";
            echo "<td>{$data['name']}</td>";
            echo "<td class='action-column' id='{$data['status']}-column'><p>{$data['status']}</p></td>";
            echo "<td class='action-column' id='{$data['arrival']}-column'><p>{$data['arrival']}</p></td>";
            echo "<td>{$birthAttendantFullName}</td>";
            echo "<td>{$data['date']} • {$data['time']}</td>";
            echo "<td class='action-column'>";
            echo "<button id='icon-btn' type='button' value='{$data['rfrrl_id']}' class='viewRecord'><i class='fi fi-rr-eye'></i></button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }
}
?>
