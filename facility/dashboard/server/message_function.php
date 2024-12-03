<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

$sql = mysqli_query($conn, "SELECT f.*, m.*
                            FROM facilities f
                            LEFT JOIN messages m ON (f.fclt_id = m.sender_id OR f.fclt_id = m.receiver_id)
                            AND m.id = (
                                SELECT MAX(id) 
                                FROM messages
                                WHERE (receiver_id = f.fclt_id OR sender_id = f.fclt_id)
                                AND (receiver_id = $fclt_id OR sender_id = $fclt_id)
                            )
                            WHERE f.fclt_id != $fclt_id AND f.verification = 'Verified'");
$output = "";

if (mysqli_num_rows($sql) == 0) {
    $output .= "No facilities are available to chat";
} else {
    // Initialize an array to store the data for sorting
    $facilitiesData = array();

    while ($row = mysqli_fetch_assoc($sql)) {
        // Fetch msg_status outside the condition
        $msgStatus = ($row !== null) ? $row['msg_status'] : '';

        // Add the facility data along with the latest message data to the array
        $facilitiesData[] = array(
            'fclt_id' => $row['fclt_id'],
            'fclt_name' => $row['fclt_name'],
            'img_url' => $row['img_url'], // Added img_url here
            'sender_id' => ($row !== null) ? $row['sender_id'] : '',
            'receiver_id' => ($row !== null) ? $row['receiver_id'] : '',
            'message' => ($row !== null && isset($row['message']) && $row['message'] !== '') ? $row['message'] : 'No message yet',
            'date' => ($row !== null) ? $row['date'] : '',
            'time' => ($row !== null) ? $row['time'] : '',
            'msg_status' => $msgStatus,
            'id' => ($row !== null) ? $row['id'] : '',
        );
    }

    // Sort the array based on the message date and time in descending order
    usort($facilitiesData, function ($a, $b) {
        return strcmp($b['date'] . ' ' . $b['time'], $a['date'] . ' ' . $a['time']);
    });

    // Generate the output based on the sorted array
    foreach ($facilitiesData as $data) {
        $you = ($fclt_id == $data['sender_id']) ? "You: " : "";

        // Check if the receiver_id is equal to $fclt_id
        $msgStatusClass = ($data['receiver_id'] == $fclt_id) ? $data['msg_status'] : '';

        // Check if there is a message, if not, set the status as an empty string
        $status = ($data['message'] == 'No message yet') ? '' : '• ' .$data['msg_status'];

        // Check if the receiver_id is equal to $fclt_id
        $newstatus = ($data['receiver_id'] == $fclt_id) ? '• New' : $status;

        $output .= '<div class="referral-card message-contact" data-contact-id="' . $data['fclt_id'] . '" data-message-id="' . $data['id'] . '">
            <div class="messages-mini-referral-logo" id="message-logo">
                <img src="../../assets/' . $data['img_url'] . '" alt="Logo">
            </div>
            <div class="info">
                <div class="name">' . $data['fclt_name'] . '</div>
                <div class="message-description ' . $msgStatusClass . '">
                    ' . $you . $data['message'] . '<div class="status">' . $newstatus . '</div>
                </div>
            </div>
        </div>';
    }
}

echo $output;
?>
