<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);
    $output = "";

    $sql = mysqli_query($conn, "SELECT * FROM facilities WHERE fclt_name LIKE '%$searchTerm%'");
    if(mysqli_num_rows($sql) > 0){
        while($row = mysqli_fetch_assoc($sql)){
            // Skip the facility if its ID matches the session ID
            if ($row['fclt_id'] == $fclt_id) {
                continue;
            }
    
            include 'data.php';
        }
    }else{
        $output .="No user found to your search term";
    }
    echo $output;