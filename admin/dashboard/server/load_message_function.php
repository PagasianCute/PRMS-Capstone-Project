<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$output = "";

    if (isset($_SESSION['fcltid'])) {
        $sender_id = mysqli_real_escape_string($conn, $_POST['sender_id']);
        $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);

            $sql = "SELECT * FROM messages INNER JOIN users ON users.usersId = messages.users_id WHERE (messages.receiver_id = '$receiver_id' AND messages.sender_id = '$sender_id') OR (messages.receiver_id = '$sender_id' AND messages.sender_id = '$receiver_id') ORDER BY id ASC";

            $query = mysqli_query($conn, $sql);
            if(mysqli_num_rows($query) > 0){
                while($row = mysqli_fetch_assoc($query)){
                    if($row['receiver_id'] === $receiver_id){
                        $output .= '<div class="sender" id="sender-messages">
                                        <div class="message-content shadow-sm" data-toggle="tooltip" data-placement="left" title="'. $row['time'] .'">
                                            <p>'. $row['message'] .'</p>
                                        </div>
                                        <div class = "users-head-logo shadow" data-toggle="tooltip" data-placement="left" title="'. $row['usersName'] .' ('. $row['usersrole'] .')">
                                            <img src="'. $row['usersImg'] .'" alt="">
                                        </div>
                                    </div>';
                    }else{
                        $output .= '<div class="receiver" id="receiver-messages">
                                        <div class = "users-head-logo shadow" data-toggle="tooltip" data-placement="top" title="'. $row['usersName'] .' ('. $row['usersrole'] .')">
                                            <img src="'. $row['usersImg'] .'" alt="">
                                        </div>
                                        <div class="message-content shadow-sm" data-toggle="tooltip" data-placement="top" title="'. $row['time'] .'">
                                            <p>'. $row['message'] .'</p>
                                        </div>
                                    </div>';
                    }
                }
                echo $output;
            }

    } else {
        echo "User not authenticated";
    }
