<?php
session_start();

include_once '../../../db/db_conn.php';
include_once '../../../config/pusher.php';

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$fclt_img = $_SESSION["fcltimg"];
$user_id = $_SESSION["usersid"];
date_default_timezone_set('Asia/Manila');
$date = date("Y-m-d");
$time = date("H:i:s");
$status = 'Sent';

if (isset($_SESSION['fcltid'])) {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $users_id = $_POST['users_id'];
    $message = $_POST['message'];

    if (empty($receiver_id)) {
        echo "Select Contact First ";
    }

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, message, receiver_id, date, time, users_id, msg_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $sender_id, $message, $receiver_id, $date, $time, $users_id, $status);
        $stmt->execute();
        echo "Message sent successfully";

        $data = array(
            'fclt_id' => $fclt_id,
            'fclt_name' => $fclt_name,
        );
        $pusher->trigger($receiver_id, 'message', $data);

        $new_message = $message;
        $type = "Message";

        date_default_timezone_set('Asia/Manila');
        $date = date("Y-m-d");
        $time = date("H:i");

        $notification_sql = "CALL notification (?, ?, ?, ?, ?, ?, ?, ?)";
        $notification_stmt = $conn->prepare($notification_sql);

        if (!$notification_stmt) {
            $response = [
                'status' => 500,
                'message' => 'Error preparing statement: ' . $conn->error,
            ];
            echo json_encode($response);
            exit;
        }else{
            echo "Notification Sent";

                    // Binding parameters to the prepared statement
        $notification_stmt->bind_param("ssiisssi", $fclt_img, $new_message, $_SESSION['fcltid'], $receiver_id, $date, $time, $type, $user_id);
        $notification_query_run = $notification_stmt->execute();
        }

    } else {
        echo "Message is empty";
    }
} else {
    echo "User not authenticated";
}
