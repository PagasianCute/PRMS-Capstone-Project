<?php
session_start();

include_once '../../../db/db_conn.php';
include_once '../../../config/pusher.php';

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

if (isset($_SESSION['fcltid'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $dataMessagetId = $_POST['dataMessagetId'];
        $dataContactId = $_POST['dataContactId'];

        $sql = mysqli_query($conn, "SELECT * FROM messages WHERE id = '$dataMessagetId' AND sender_id = '$fclt_id'");
        
        if(mysqli_num_rows($sql) > 0){
            return;
        }

        $sql = mysqli_query($conn, "SELECT * FROM messages WHERE id = '$dataMessagetId' AND msg_status = 'Seen'");
        
        if(mysqli_num_rows($sql) > 0){
            return;
        }
        
        else{
            
            $sql = "UPDATE messages SET msg_status = 'Seen' WHERE receiver_id = ? AND sender_id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param($stmt, 'ii', $fclt_id, $dataContactId);

            if (mysqli_stmt_execute($stmt)) {
                echo "Update successful";
                $data = $fclt_name;
                $pusher->trigger($dataContactId, 'message', $data);
            } else {
                echo "Update failed";
            }

            mysqli_stmt_close($stmt);
        }
    }
} else {
    echo "User not authenticated";
}