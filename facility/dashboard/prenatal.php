<?php
include_once 'header.php';
include_once 'includes/prenatal_functions.inc.php';

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
    <h2>Prenatal List</h2>
  </div>
  <div class="table-header">
 <div class="col-2">
  <input type="text" name="address" id="address " class="form-control" placeholder="Search">
  </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary filterBtn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
  </div>
  <div class="head_buttons">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fi fi-br-plus"></i>Add Patient</button>
    </div>  
 </div>

 <div class="prenatal_table">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Barangay</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Loop through the paginated patients and display each in a table row
      foreach ($patients as $key => $patient) {
        $id = $patient['id'];
        $fname = $patient['fname'];
        $mname = $patient['mname'];
        $lname = $patient['lname'];
        $address = $patient['barangay'];
        ?>


        <tr>
          <th scope='row'><?php echo (($page - 1) * $itemsPerPage + $key + 1)?></th>
          <td><?php echo $fname?></td>
          <td><?php echo $lname?></td>
          <td><?php echo $address?></td>
          <td>
            <a class="btn btn-primary table-btn createNewPrenatalRecord" data-toggle="tooltip" data-placement="left" title="Add Record" href="view_prenatal.php?id=<?php echo $id?>" data-patient-id="<?php echo $id?>" role="button"><i class="fi fi-rr-add-folder"></i></a>
            <button type="button" class="btn btn-primary table-btn viewPatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Edit (View)"><i class="fi fi-rs-pencil"></i></button>
            <button type="button" class="btn btn-primary table-btn deletePatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
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


<!-- ADD PATIENT  -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Patient's Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
        <form id="addPatient">
          <div class="mb-3">
            <label for="fname">First Name</label>
            <input class="form-control" type="text" name="fname" id="fname" placeholder="First Name" required>
          </div>
          <div class="mb-3">
          <label for="mname">Middle Name</label>
            <input class="form-control" type="text" name="mname" id="mname" placeholder="Middle Name (optional)">
          </div>
          <div class="mb-3">
          <label for="lname">Last Name</label>
            <input class="form-control" type="text" name="lname"  id="lname" placeholder="last Name" required>
          </div>
          <div class="mb-3">
          <label for="contactNum">Contact Number</label>
            <input class="form-control" type="tex" name="contactNum" id="contactNum" placeholder="Contact Number" required>
          </div>
          <div class="mb-3">
          <label for="address">Address</label>
            <input class="form-control" type="tex" name="address" id="address" placeholder="Address" required>
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="submit">Add Patient</button>
              </div>
        </form>
    </div>
  </div>
</div>


<!-- VIEW PATIENT DETAILS -->
<div class="modal fade" id="viewPatientModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">View Patient's Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12">
            <form id="UpdatePatient">
              <div class="row">
              <div class="mb-3 col-lg-6">
                <label for="fname">First Name</label>
                <input class="form-control" type="text" name="fname" id="view_fname">
              </div>
              <div class="mb-3 col-lg-6">
              <label for="mname">Middle Name</label>
                <input class="form-control" type="text" name="mname" id="view_mname">
              </div>
              <div class="mb-3 col-lg-6">
              <label for="lname">Last Name</label>
                <input class="form-control" type="text" name="lname"  id="view_lname">
              </div>
              <div class="mb-3 col-lg-6">
              <label for="contactNum">Contact Number</label>
                <input class="form-control" type="tex" name="contactNum" id="view_contactNum">
              </div>
              <div class="mb-3 col-lg-6">
              <label for="address">Address</label>
                <input class="form-control" type="tex" name="address" id="view_address">
              </div>
              </div>
              <div class="alert alert-danger d-none" id="errorMessage"></div>
          </div>
            <h5>Records</h5>
            <div class="col-lg-12 p-3 records">
            <div class="row row-cols-2 row-cols-lg-8 g-2 g-lg-3 records-list">
                  <!-- PATIENT RECORDS DISPLAY -->
            </div>
            </div>
          </div>
      </div>
            <div class="modal-footer">
              <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit">Update Patient</button>
            </div>
          </form>

    </div>
  </div>
</div>


<!-- VIEW PATIENT RECORDS -->
<div class="modal fade" id="viewPatientRecordsModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">View Patient's Records</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
          <!-- DISPLAY PATIENTS RECORDS HERE -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
        <a class="btn btn-primary createNewPrenatalRecord" href="view_prenatal.php" data-patient-id="" role="button">Create New Record</a>
      </div>
    </div>
  </div>
</div>


<?php
include_once 'footer.php';
?>