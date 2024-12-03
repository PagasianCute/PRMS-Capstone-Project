<?php
include_once 'header.php';
?>
  <h1>Display Form Fields</h1>
  <form method="post" action="insert_form_values.php">
    <?php
    // Include the database connection file
    include 'db_conn.php';

    // Fetch data from the 'myform' table
    $sql = "SELECT label FROM myform";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $label = $row['label'];
        echo '<div>';
        echo '  <label>' . htmlspecialchars($label) . '</label>';
        echo '  <input type="text" name="' . htmlspecialchars($label) . '">';
        echo '</div>';
      }
    } else {
      echo '<p>No form fields found.</p>';
    }

    // Close the database connection

    ?>
    <button type="submit">Submit</button>
  </form>

  <?php
include 'db_conn.php';

    $query = "SELECT label FROM myform";
    $query_run = mysqli_query($conn, $query);

    $dataArray = array();

    if (mysqli_num_rows($query_run) > 0) {
        while ($row = mysqli_fetch_assoc($query_run)) {
            foreach ($row as $field => $value) {
                // Add each value to the $dataArray
                $dataArray[] = $value;
            }
        }
    }

// Separate array data by commas
$dataString = implode(",", $dataArray);
$questionMarks = implode(', ', array_fill(0, count($dataArray), '?'));
$placeholders = '"' . implode(',', array_fill(0, count($dataArray), 's')) . '"';

// Output the result
echo $dataString;
echo $questionMarks;
echo $placeholders;

$conn->close();
?>

  <?php
include_once 'footer.php';
?>