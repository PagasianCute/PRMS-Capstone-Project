<?php
$sql2 = "SELECT * FROM messages WHERE (receiver_id = {$row['fclt_id']} OR sender_id = {$row['fclt_id']}) AND (receiver_id = {$fclt_id} OR sender_id = {$fclt_id}) ORDER BY id DESC LIMIT 1";
$query2 = mysqli_query($conn, $sql2);
$row2 = mysqli_fetch_assoc($query2);

if ($row2 !== null) {
    $result = $row2['message'];

    if (strlen($result) > 28) {
        $message = substr($result, 0, 28) . '...';
    } else {
        $message = $result;
    }

    $you = ($fclt_id == $row2['sender_id']) ? "You: " : "";

    if($row2['receiver_id'] == $fclt_id){
        $status = $row2['msg_status'];
    }else{
        $status = "";
    }

    $output .= '<div class="referral-card message-contact">
                    <div class="mini-referral-logo" id="message-logo">
                        <img src="assets/person.png" alt="Logo" class="logo">
                    </div>
                    <div class="info">
                        <div class="name">' . $row['fclt_name'] . '</div>
                        <div class="message-description '.$status.'">
                        ' . $you . $message . ' <div class="status"> • '.$row2['msg_status'].'</div>
                        </div>
                    </div>
                    <button class="confirm-button" id="viewbtn" value="' . $row['fclt_id'] . '">View</button>
                </div>';
} else {
    $output .= '<div class="referral-card message-contact">
                    <div class="mini-referral-logo" id="message-logo">
                        <img src="assets/person.png" alt="Logo" class="logo">
                    </div>
                    <div class="info">
                        <div class="name">' . $row['fclt_name'] . '</div>
                        <div class="description">
                        No message yet
                        </div>
                    </div>
                    <button class="confirm-button" id="viewbtn" value="' . $row['fclt_id'] . '">View</button>
                </div>';
}