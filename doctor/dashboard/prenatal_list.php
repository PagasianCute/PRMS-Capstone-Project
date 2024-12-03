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

<div class="feed" id="patients_list">
  <div class="head">
    <h2>Prenatal List</h2>
    <div class="head_buttons">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fi fi-br-plus"></i> Add Patient</button>
    </div>  
  </div>
  <div class="prenatal_table">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Full Name</th>
        <th scope="col">Address</th>
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
        $address = $patient['address'];

        echo "<tr>";
        echo "<th scope='row'>" . (($page - 1) * $itemsPerPage + $key + 1) . "</th>";
        echo "<td>$lname, $fname $mname</td>";
        echo "<td>$address</td>";
        echo '<td>
                <button type="button" class="btn btn-primary viewPatient" value="' . $id . '">View</button>
                <button type="button" class="btn btn-primary viewPatientRecords" value="' . $id . '">View Records</button>
                <button type="button" class="btn btn-primary" value="' . $id . '" id="deletePatient">Delete</button>
              </td>';
        echo "</tr>";
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">View Patient's Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
        <form id="UpdatePatient">
          <div class="mb-3">
            <label for="fname">First Name</label>
            <input class="form-control" type="text" name="fname" id="view_fname">
          </div>
          <div class="mb-3">
          <label for="mname">Middle Name</label>
            <input class="form-control" type="text" name="mname" id="view_mname">
          </div>
          <div class="mb-3">
          <label for="lname">Last Name</label>
            <input class="form-control" type="text" name="lname"  id="view_lname">
          </div>
          <div class="mb-3">
          <label for="contactNum">Contact Number</label>
            <input class="form-control" type="tex" name="contactNum" id="view_contactNum">
          </div>
          <div class="mb-3">
          <label for="address">Address</label>
            <input class="form-control" type="tex" name="address" id="view_address">
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>
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
          <div class="alert alert-primary d-flex" role="alert">
            <h6 style="margin-top: 8px;">A simple primary alert—check it out!</h6>
            <a class="btn btn-primary" href="view_prenatal.php" id="viewRecordBtn" style="margin-left: auto;" role="button">View</a>
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
            <a class="btn btn-primary" href="add_prenatal.php" id="createRecordBtn" role="button">Create New Record</a>
          </div>
    </div>
  </div>
</div>


    <?php
include_once 'footer.php'
?>