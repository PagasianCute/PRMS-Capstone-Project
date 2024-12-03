<?php
include_once 'header.php';
include_once 'includes/patient_functions.inc.php';

// Define the number of items per page
$itemsPerPage = 9;

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Call the function and fetch paginated referrals
$patients = getPaginatedPatients($page, $itemsPerPage);

?>

<script src="js/ajaxPrenatal.js"></script>

<div class="feed" id="patients_list">
  <div class="head">
    <h2>Patient List</h2>
  </div>
  <div class="table-header">
        <div class="col-2">
            <input type="text" id="search-input" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-search">
        </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary filterBtn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
  </div> 
 </div>

 <div class="prenatal_table">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">First Name</th>
        <th scope="col">MIddle Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">From</th>
        <th scope="col">Barangay</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody id="table-body">
      <?php
      // Loop through the paginated patients and display each in a table row
      foreach ($patients as $key => $patient) {
        $id = $patient['id'];
        $fname = $patient['fname'];
        $mname = $patient['mname'];
        $lname = $patient['lname'];
        $address = $patient['barangay'];   
        $fclt_name = $patient['fclt_name'];        
        ?>


        <tr>
          <th scope='row'><?php echo (($page - 1) * $itemsPerPage + $key + 1)?></th>
          <td><?php echo $fname?></td>
          <td><?php echo $mname?></td>
          <td><?php echo $lname?></td>
          <td><?php echo $fclt_name?></td>
          <td><?php echo $address?></td>
          <td>
            <a class="btn btn-primary table-btn" data-toggle="tooltip" data-placement="left" title="View Patient" href="view_patient.php?id=<?php echo $id?>" data-patient-id="<?php echo $id?>" role="button"><i class="fi fi-rr-eye"></i></a>
          </td>
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

<script>
    $(document).ready(function(){
        $('#search-input').on("keyup", function(){
            var searchTerm = $(this).val();
            $.ajax({
                method:'POST',
                url:'server/search-patient-list.php',
                data:{searchTerm: searchTerm},
                success:function(response)
                {
                    $("#table-body").html(response);
                } 
            });
        });
    });
</script>


<?php
include_once 'footer.php';
?>