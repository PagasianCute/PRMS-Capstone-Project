<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

$sql = mysqli_query($conn, "SELECT * FROM facilities");
$output = "";

if(mysqli_num_rows($sql) == 1){
    $output .= "No facilities are available to chat";
} else if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
        // Skip the facility if its ID matches the session ID
        if ($row['fclt_id'] == $fclt_id) {
            continue;
        }

        include 'index_data.php';
    }
}
echo $output;