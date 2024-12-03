<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$output = "";

    if (isset($_SESSION['fcltid'])) {
        $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);

        $sql = "SELECT * FROM facilities WHERE fclt_id = '$receiver_id'";

        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
        $row = mysqli_fetch_assoc($query);
            $output .= '<div class="messages-head">
                            <button type="button" class="btn btn-primary" id="back-btn">
                                <i class="fi fi-ts-arrow-circle-left"></i>
                            </button>
                            <div class="messages-head-logo" id="message-logo">
                                <img src="assets/person.png" alt="Logo" class="logo">
                            </div>
                            <div class="info">
                                <div class="name" id="contact_name">
                                    '. $row['fclt_name'] .'
                                </div>
                                <div class="description">'. $row['fclt_status'] .'</div>
                            </div>
                        </div>';
        }else{
            $output .= '<div class="messages-head">
                            <div class="messages-head-logo" id="message-logo">
                                <img src="assets/person.png" alt="Logo" class="logo">
                            </div>
                            <div class="info">
                                <div class="name" id="contact_name">
                                    Contact Name
                                </div>
                                <div class="description">Status</div>
                            </div>
                        </div>';
        }
        
        echo $output;

    } else {
        echo "User not authenticated";
    }
