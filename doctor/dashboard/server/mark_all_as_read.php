<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["fcltid"])) {
  $fclt_id = $_POST["fcltid"];

  $updateSql = "UPDATE referral_notification SET is_displayed = 1 WHERE to_fclt_id = $fclt_id";
  $result = mysqli_query($conn, $updateSql);

  if ($result) {
    echo "All notifications marked as read successfully.";
  } else {
    echo "Error marking all notifications as read: " . mysqli_error($conn);
  }
} else {
  // Invalid request
  echo "Invalid request.";
}
?>
