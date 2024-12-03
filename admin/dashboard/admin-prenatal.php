<?php
include_once 'header.php';
include_once 'includes/prenatal_functions.inc.php';

// Define the number of items per page
$itemsPerPage = 9;

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Call the function and fetch paginated referrals
$patients = getPaginatedPatients($page, $itemsPerPage);
$currentYear = date("Y");
$sql_old = "SELECT COUNT(*) as row_count FROM patients WHERE YEAR(date_registered) != '$currentYear' AND fclt_id = '$fclt_id'";
$sql_new = "SELECT COUNT(*) as row_count FROM patients WHERE YEAR(date_registered) = '$currentYear' AND fclt_id = '$fclt_id'";
$result_old = mysqli_query($conn, $sql_old);
$result_new = mysqli_query($conn, $sql_new);

if ($result_old && $result_new) {
  $referral_old = mysqli_fetch_assoc($result_old);
  $referral_new = mysqli_fetch_assoc($result_new);

  $referral_old = $referral_old['row_count'];
  $referral_new = $referral_new['row_count'];
}

?>

<script src="js/ajaxPrenatal.js"></script>
<div class="main-cards">
  <div class="mini-cards">
    <div class="mini-cards-header">
      <div class="mini-logo" id="old">
      <i class="fi fi-rr-form"></i>
      </div>
      <h6>Old Patients</h6>
    </div>
    <div class="mini-card-content">
      <h2 class="mini-name"><?php echo $referral_old ?></h2>
      <p class="mini-description">Past Years</p>
    </div>
  </div>
  <div class="mini-cards">
    <div class="mini-cards-header">
      <div class="mini-logo" id="new">
      <i class="fi fi-rr-form"></i>
      </div>
      <h6>New Patients</h6>
    </div>
    <div class="mini-card-content">
      <h2 class="mini-name"><?php echo $referral_new ?></h2>
      <p class="mini-description">This Year</p>
    </div>
  </div>
</div>

<div class="feed" id="patients_list">
  <div class="head">
    <h2>Prenatal List</h2> 
  </div>
  <div class="table-header">
 <div class="col-2">
  <input type="text" name="address" id="address " class="form-control" placeholder="Search">
  </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
  </div>
 </div>
  <div class="prenatal_table">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Address</th>
        <th scope="col">Record</th>
        <th scope="col">Health Provider Name</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Loop through the paginated patients and display each in a table row
      foreach ($patients as $key => $patient) {
        $fname = $patient['patient_fname'];
        $mname = $patient['patient_mname'];
        $lname = $patient['patient_lname'];
        $address = $patient['barangay'];
        $records_count = $patient['records_count'];
        $health_provider = $patient['staff_fname'] . ', '. $patient['staff_mname'] .' '. $patient['staff_lname'];
        ?>


        <tr>
          <th scope='row'><?php echo (($page - 1) * $itemsPerPage + $key + 1)?></th>
          <td><?php echo $fname?></td>
          <td><?php echo $lname?></td>
          <td><?php echo $address?></td>
          <td><?php echo $records_count?></td>
          <td><?php echo $health_provider?></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  </div>

  <?php
  // Display pagination controls
  $totalPages = ceil(getTotalPatients() / $itemsPerPage);

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
include_once 'footer.php';
?>