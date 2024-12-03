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
    $sql = "SELECT referral_forms.*, referral_records.*, facilities.*
            FROM referral_forms
            INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
            INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital
            WHERE referral_records.fclt_id = $fclt_id
            AND (referral_forms.id LIKE '%$searchTerm%'
                OR facilities.fclt_name LIKE '%$searchTerm%'
                OR referral_forms.name LIKE '%$searchTerm%'
                OR referral_records.status LIKE '%$searchTerm%')
            ORDER BY referral_forms.id DESC";

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

        // Output the filtered rows
        foreach ($referrals as $key => $referral) {
            echo "<tr>";
            echo "<th scope='row'>{$referral['rfrrl_id']}</th>";
            echo "<td>{$referral['fclt_name']}</td>";
            echo "<td>{$referral['name']}</td>";
            echo "<td class='action-column' id='{$referral['status']}-column'><p>{$referral['status']}</p></td>";
            // Display date and time from referral_records table
            echo "<td>{$referral['date']} • {$referral['time']}</td>";
            echo "<td class='action-column'>";
            echo "<button id='icon-btn' type='button' value='{$referral['rfrrl_id']}' class='viewMyRecord'><i class='fi fi-rr-eye'></i></button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        // Handle query error (you may choose to log or display an error message)
        echo "Error executing query: " . mysqli_error($conn);
    }
}
?>
