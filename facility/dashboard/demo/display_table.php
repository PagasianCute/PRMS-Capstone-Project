<?php
include_once 'header.php';
?>

  <h1 style="text-align:center">Form Values Table</h1>
  <div class="container">

  
  <table>
    <tr>
    <th>ID</th>
<?php 
      include 'db_conn.php';
      $query = "SELECT * FROM myform";
      $query_run = mysqli_query($conn, $query);

      if(mysqli_num_rows($query_run) > 0){
        foreach($query_run as $field){
          ?>

        <th><?=  $field['label'] ?></th>
          <?php 
        }
      }
?>

     

    </tr>
    <?php
    // Include the database connection file
    include 'db_conn.php';

    // Fetch data from the 'form_values' table
    $sql = "SELECT * FROM mycolumn";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '  <td>' . htmlspecialchars($row['id']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Name']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Age']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Sex']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Sport']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['asadad']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['sheesh']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['deym']) . '</td>';
        echo '</tr>';
      }
    } else {
      echo '<tr><td colspan="3">No data found in the table.</td></tr>';
    }

    // Close the database connection
    $conn->close();
    ?>
  </table>
  </div>

  <br>

  <?php
    // Include the database connection file
    include 'db_conn.php';

    // Fetch data from the 'form_values' table
    $sql = "SELECT * FROM form_values GROUP BY label";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '  <div class="container">
        <div class="card">
        <h5 class="card-header">' . htmlspecialchars($row['label']) . '</h5>
        <div class="card-body">
          <h5 class="card-title">Special title treatment</h5>
          <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
          <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
      </div>
        </div>';
      }
    } else {
      echo '<tr><td colspan="3">No data found in the table.</td></tr>';
    }

    // Close the database connection
    $conn->close();
    ?>



  <?php
include_once 'footer.php';
?>
