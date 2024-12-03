<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$output = "";

if (isset($_SESSION['fcltid'])) {
  $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
  $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;

  $sql = "SELECT 
  referral_notification.*,
  from_facilities.fclt_name AS from_fclt_name,
  to_facilities.fclt_name AS to_fclt_name
FROM 
  referral_notification
INNER JOIN 
  facilities AS from_facilities ON referral_notification.from_fclt_id = from_facilities.fclt_id
INNER JOIN 
  facilities AS to_facilities ON referral_notification.to_fclt_id = to_facilities.fclt_id
ORDER BY id DESC LIMIT $limit OFFSET $offset";


  $query = mysqli_query($conn, $sql);

  if (!$query) {
    die('Query error: ' . mysqli_error($conn));
  }

  // Check if there is data
  if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
      $formattedTime = date("h:i A", strtotime($row['time']));
      $formattedDate = date("M jS", strtotime($row['date']));

      if ($row['to_fclt_id'] == $fclt_id) {
        if($row['type'] == 'Patient Arrival' || $row['type'] == 'Referral'){
          $output .= '<a class="notification-card" href="#">
              <div class="notification-logo">
                <img src="../../assets/' . $row['icon'] . '" alt="facility logo">
              </div>
              <div class="notification-caption">
                <p>'. $row['message'] .' at '. $row['from_fclt_name'] .'</p>
              </div>
              <div class="notification-status">
              ' . $formattedDate. ' • ' . $formattedTime . '
              </div>
            </a>';
        }else if($row['type'] == 'Referral Process'){
          $output .= '<a class="notification-card" href="#">
                <div class="notification-logo">
                  <img src="../../assets/' . $row['icon'] . '" alt="facility logo">
                </div>
                <div class="notification-caption">
                  <p>'. $row['message'] .' by '. $row['from_fclt_name'] .'</p>
                </div>
                <div class="notification-status">
                ' . $formattedDate. ' • ' . $formattedTime . '
                </div>
              </a>';
        }else if($row['type'] == 'Referral Transaction'){
          $output .= '<a class="notification-card" href="#">
                <div class="notification-logo">
                  <img src="../../assets/' . $row['icon'] . '" alt="facility logo">
                </div>
                <div class="notification-caption">
                  <p>'. $row['message'] .' by '. $row['from_fclt_name'] .'</p>
                </div>
                <div class="notification-status">
                ' . $formattedDate. ' • ' . $formattedTime . '
                </div>
              </a>';
        }else if($row['type'] == 'Referral Accepted'){
          $output .= '<a class="notification-card" href="#">
                <div class="notification-logo">
                  <img src="../../assets/' . $row['icon'] . '" alt="facility logo">
                </div>
                <div class="notification-caption">
                  <p>'. $row['message'] .' by '. $row['from_fclt_name'] .'</p>
                </div>
                <div class="notification-status">
                ' . $formattedDate. ' • ' . $formattedTime . '
                </div>
              </a>';
        }
        else if($row['type'] == 'Referral Declined'){
          $output .= '<a class="notification-card" href="#">
                <div class="notification-logo">
                  <img src="../../assets/' . $row['icon'] . '" alt="facility logo">
                </div>
                <div class="notification-caption">
                  <p>Referral Declined by '. $row['from_fclt_name'] .' for a reason: '. $row['message'] .'</p>
                </div>
                <div class="notification-status">
                ' . $formattedDate. ' • ' . $formattedTime . '
                </div>
              </a>';
        }
      } else if ($row['from_fclt_id'] == $fclt_id) {
        if($row['type'] == 'Referral'){
          $output .= '<a class="notification-card" href="#">
                <div class="notification-logo">
                  <img src="../../assets/' . $row['icon'] . '" alt="facility logo">
                </div>
                <div class="notification-caption">
                  <p>Referral Submitted to ' . $row['to_fclt_name'] . '</p>
                </div>
                <div class="notification-status">
                ' . $formattedDate. ' • ' . $formattedTime . '
                </div>
              </a>';
        }
      }
    }
  }

  echo $output;
} else {
  echo "User not authenticated";
}
?>
