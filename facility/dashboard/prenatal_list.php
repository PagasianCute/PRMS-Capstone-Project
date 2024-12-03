<?php
include_once 'header.php';
include_once 'includes/prenatal_functions.inc.php';

// Define the number of items per page
$itemsPerPage = 9;

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Call the function and fetch paginated referrals
$patients = getPrenatalRecords($page, $itemsPerPage);

?>

<div class="feed" id="patients_list">
  <div class="head">
    <h2>Prenatal List</h2>
  </div>
  <div class="prenatal_table">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Facility Name</th>
        <th scope="col">Total Prenatal</th>
        <th scope="col">Total Patients</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Loop through the paginated patients and display each in a table row
      foreach ($patients as $key => $patient) {
        $fclt_name = $patient['fclt_name'];
        $prenatal_count = $patient['prenatal_records_count'];
        $patients_count = $patient['patients_count'];

        ?>

      <tr>
          <th scope='row'><?php echo (($page - 1) * $itemsPerPage + $key + 1)?></th>
          <td><?php echo $fclt_name?></td>
          <td><?php echo $prenatal_count?></td>
          <td><?php echo $patients_count?></td>
        </tr>

        <?php
      }
      ?>
    </tbody>
  </table>
  </div>

  <?php
  // Display pagination controls
  $totalPages = ceil(getTotalPrenatal() / $itemsPerPage);

  echo '<nav aria-label="Page navigation" id="nav_buttons">
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="?page=1">&laquo; First</a></li>';
  for ($i = 1; $i <= $totalPages; $i++) {
    echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
  }
  echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">Last &raquo;</a></li>
        </ul>
    </nav>';
  ?>
</div>


    <?php
include_once 'footer.php'
?>