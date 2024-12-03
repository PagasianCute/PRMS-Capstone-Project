<?php
include_once 'header.php';
include_once 'includes/prenatal_functions.inc.php';

// Define the number of items per page
$itemsPerPage = 9;

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Call the function and fetch paginated referrals
$patients = getPaginatedPatients($page, $itemsPerPage);

$region_code = $_SESSION["fcltregioncode"];
$region = $_SESSION["fcltregion"];
$province = $_SESSION["fcltprovince"];
$municipality = $_SESSION["fcltmunicipality"];

// Load JSON file content
$json_content = file_get_contents('philippine_provinces_cities_municipalities_and_barangays_2019v2.json');
$data = json_decode($json_content, true);

// Extract barangay list based on region, province, and municipality
$barangay_list = [];
if (
    isset($data[$region_code]['province_list'][$province]['municipality_list'][$municipality]['barangay_list'])
) {
    $barangay_list = $data[$region_code]['province_list'][$province]['municipality_list'][$municipality]['barangay_list'];
}
?>

<div class="feed" id="patients_list">
  <div class="head">
    <h2>Your Patient List</h2>
  </div>
  <div class="table-header">
 <div class="col-2">
    <input type="text" id="search-input" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-search">
  </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary filterBtn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Search</button>
  </div>
  <div class="head_buttons">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fi fi-br-plus"></i>Add Patient</button>
    </div>  
 </div>

 <div class="prenatal_table table-responsive">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Barangay</th>
        <th scope="col">Records</th>
        <th scope="col">Latest Record Date</th>
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
        $records = $patient['prenatal_records_count'];
        $newrecords = $patient['prenatal_records_count'] + 1;
        $latest_record_date = $patient['latest_record_date'];

        if (!empty($latest_record_date)) {
          $latest_record_dateTimestamp = strtotime($latest_record_date);
          $formattedlatest_record_date = date('M jS, Y', $latest_record_dateTimestamp);
        } else {
            // Handle empty date case
            $formattedlatest_record_date = "No Records";
        }
        ?>


        <tr>
          <th scope='row'><?php echo (($page - 1) * $itemsPerPage + $key + 1)?></th>
          <td><?php echo $fname?></td>
          <td><?php echo $lname?></td>
          <td><?php echo $address?></td>
          <td><?php echo $records?></td>
          <td><?php echo $formattedlatest_record_date?></td>
          <td>
          <a class="btn btn-primary table-btn" data-toggle="tooltip" data-placement="left" title="View Patient" href="view_patient.php?id=<?php echo $id?>" data-patient-id="<?php echo $id?>" role="button">
                                <i class="fi fi-rr-eye"></i>
                            </a>
            <button type="button" class="btn btn-primary table-btn viewPatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="View Records"><i class="fi fi-rs-pencil"></i></button>
            <button class="btn btn-primary table-btn createNewPrenatalRecord" data-toggle="tooltip" data-placement="left" title="Add Record" value="<?php echo $id?>">
            <i class="fi fi-rr-add-folder"></i>
          </button>
          <button type="button" class="btn btn-primary table-btn deletePatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete">
              <i class="fi fi-rs-trash"></i>
          </button>
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
          <div class="row">
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
          <label for="address">Sex</label>
          <select class="form-select" name="gender" id="gender" required>
            <option disabled selected value="">Select gender</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
          </select>
          </div>
          <div class="mb-3">
          <label for="lname">Email</label>
            <input class="form-control" type="text" name="email"  id="email" placeholder="Email (optional)">
          </div>
          <div class="col-12 mb-3">
          <label for="contactNum">Contact Number</label>
            <input class="form-control" type="tex" name="contactNum" id="contactNum" placeholder="Contact Number" required>
          </div>
          <div class="mb-3">
          <label for="lname">Birth Date</label>
            <input class="form-control" type="date" name="birthdate"  id="birthdate" required>
          </div>
          <div class="mb-3">
          <label for="address">Barangay</label>
          <select class="form-select" aria-label="Default select example" name="barangay" id="barangay-select" required>
            <option disabled selected value="">Select barangay</option>
            <!-- BARANGAYS WILL DISPLAY HERE -->
          </select>
          </div>
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

<!-- VIEW ARRIVAL REFERRALS -->
<div class="modal fade" id="recordConfirmation" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
          <div class="modal-body">
            <h4 class="p-2 text-center">Are you sure you want to add Prenatal Record?</h4>
          </div>
      <div class="modal-footer">
        <div class="referral-buttons">
          <button type="button" data-bs-dismiss="modal" class="btn close">Cancel</button>
          <button type="button" class="btn btn-primary recordConfirmed">Yes</button>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- VIEW PATIENT DETAILS -->
<div class="modal fade" id="viewPatientModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Patient's Prenatal Records</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <h2 class="accordion-header">
            <h5>Records</h5>
            </button>
          </h2>
            <div class="col-lg-12 p-3 records">
              <div class="row row-cols-2 row-cols-lg-8 g-2 g-lg-3 records-list">
                    <!-- PATIENT RECORDS DISPLAY -->
              </div>
            </div>
          </div>
      </div>
            <div class="modal-footer">
              <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
              <a role="button" href="#" class="btn btn-primary" id="view-profile">View Profile</a>
            </div>
          </form>
    </div>
  </div>
</div>


<script src="js/ajaxPrenatal.js"></script>

<script>
    var $barangayList = <?php echo json_encode($barangay_list); ?>;
    
    // Assuming you have a <select> element with id "barangay-select"
    var $barangaySelect = document.getElementById("barangay-select");

    // Populate options based on the extracted barangay list
    for (var i = 0; i < $barangayList.length; i++) {
        var option = document.createElement("option");
        option.value = $barangayList[i];
        option.text = $barangayList[i];
        $barangaySelect.appendChild(option);
    }
</script>

<script>
    $(document).ready(function(){
        $('#search-input').on("keyup", function(){
            var searchTerm = $(this).val();
            $.ajax({
                method:'POST',
                url:'server/search-prenatal-list.php',
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