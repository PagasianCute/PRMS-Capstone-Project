<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['adminuid'];

$sql = mysqli_query($conn, "SELECT * FROM facilities");
$output = "";

if(mysqli_num_rows($sql) == 1){
    $output .= "No facilities are available to chat";
} else if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
        include 'data.php';
    }
}
echo $output;