<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$output = "";
$lastUserId = null; // Variable to keep track of the last user ID

if (isset($_SESSION['fcltid'])) {
    $sender_id = mysqli_real_escape_string($conn, $_POST['sender_id']);
    $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "SELECT * FROM messages INNER JOIN staff ON staff.staff_id = messages.users_id WHERE (messages.receiver_id = '$receiver_id' AND messages.sender_id = '$sender_id') OR (messages.receiver_id = '$sender_id' AND messages.sender_id = '$receiver_id') ORDER BY id ASC";

    $query = mysqli_query($conn, $sql);
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            // Convert the time to AM/PM format without seconds
            $formattedTime = date('h:i A', strtotime($row['time']));
            
            // Format the date as 'M. jS Y'
            $formattedDate = date('M. jS Y', strtotime($row['date']));

            $currentUserId = $row['users_id'];

            // Display user name if it's a new user
            if ($currentUserId !== $lastUserId) {
                if ($row['sender_id'] === $sender_id) {
                    $output .= '<span class="senders_name">' . $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] . ' (' . $row['role'] . ')</span>';
                } else {
                    $output .= '<span class="receivers_name">' . $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] . ' (' . $row['role'] . ')</span>';
                }
                $lastUserId = $currentUserId;
            }

            // Display message content
            if ($row['sender_id'] === $sender_id) {
                $output .= '<div class="sender" id="messages">
                                <div class="message-content shadow-sm" data-toggle="tooltip" data-placement="left" title="' . $formattedDate . ' ' . $formattedTime . '">
                                    <p>' . $row['message'] . '</p>
                                </div>
                                <div class="users-head-logo shadow" data-toggle="tooltip" data-placement="left" title="' . $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] . ' (' . $row['role'] . ')">
                                    <img src="../../assets/' . $row['img'] . '" alt="">
                                </div>
                            </div>';
            } else {
                $output .= '<div class="receiver" id="messages">
                                <div class="users-head-logo shadow" data-toggle="tooltip" data-placement="top" title="' . $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] . ' (' . $row['role'] . ')">
                                    <img src="../../assets/' . $row['img'] . '" alt="">
                                </div>
                                <div class="message-content shadow-sm" data-toggle="tooltip" data-placement="top" title="' . $formattedDate . ' ' . $formattedTime . '">
                                    <p>' . $row['message'] . '</p>
                                </div>
                            </div>';
            }
        }
        echo $output;
    }

} else {
    echo "User not authenticated";
}
?>
