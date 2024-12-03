<?php
include_once 'header.php';
include_once 'includes/inventory_functions.inc.php';

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
    <h2>Your Inventory</h2>
  </div>
  <div class="table-header">
 <div class="col-2">
    <input type="text" id="search-input" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-search">
  </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary filterBtn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Search</button>
  </div>
  <div class="head_buttons">
    <button type="button" class="btn btn-primary" id="buttonCreate"><i class="fi fi-br-plus"></i>Add Record</button>
    </div>  
 </div>

 <div class="prenatal_table">
  <table class="table table-hover" id="table" style="text-align: center;">
    <thead>
      <tr>
        <th scope="col">Case Number</th>
        <th scope="col">First Name</th>
        <th scope="col">Middle Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Birth Attendant</th>
        <th scope="col">Barangay</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody id="table-body">
      <?php
      // Loop through the paginated patients and display each in a table row
      foreach ($patients as $key => $patient) {
        $id = $patient['case_number'];
        $fname = $patient['fname'];
        $mname = $patient['mname'];
        $lname = $patient['lname'];
        $birth_attendant = $patient['staff_lname']. ', ' . $patient['staff_fname'] . ' ' . $patient['staff_mname'];
        $barangay = $patient['barangay'];

        if (!empty($latest_record_date)) {
          $latest_record_dateTimestamp = strtotime($latest_record_date);
          $formattedlatest_record_date = date('M jS, Y', $latest_record_dateTimestamp);
        } else {
            // Handle empty date case
            $formattedlatest_record_date = "No Records";
        }
        ?>


        <tr>
          <td><?php echo $id?></td>
          <td><?php echo $fname?></td>
          <td><?php echo $mname?></td>
          <td><?php echo $lname?></td>
          <td><?php echo $birth_attendant?></td>
          <td><?php echo $barangay?></td>
          <td>
            <button type="button" class="btn btn-primary table-btn viewRecord" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="View Records"><i class="fi fi-rs-pencil"></i></button>
            <button type="button" class="btn btn-primary table-btn deleteRecord" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
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


<!-- ADD Record  -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Birthing Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
        <form id="addRecord">
          <div class="row">
          <input class="form-control" type="hidden" name="case_number" id="case_number">
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Date of Admission</label>
            <input class="form-control" type="date" name="admission_date" id="admission_date" placeholder="Date of Admission" required>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Time of Admission</label>
            <input class="form-control" type="time" name="admission_time" id="admission_time" placeholder="Time of Admission" required>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Last Name</label>
            <input class="form-control" type="text" name="lname"  id="lname" placeholder="Last Name" required>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>First Name</label>
            <input class="form-control" type="text" name="fname"  id="fname" placeholder="First Name" required>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Middle Name</label>
            <input class="form-control" type="text" name="mname"  id="mname" placeholder="Middle Name (Optional)">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Date of Birth</label>
            <input class="form-control" type="date" name="birth_date" id="birth_date" placeholder="Date of Birth">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label for="address">Barangay</label>
          <select class="form-select" aria-label="Default select example" name="barangay" id="barangay-select" required>
            <option disabled selected value="">Select barangay</option>
            <!-- BARANGAYS WILL DISPLAY HERE -->
          </select>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Orvida</label>
            <input class="form-control" type="text" name="orvida"  id="orvida" placeholder="Orvida">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Para</label>
            <input class="form-control" type="text" name="para"  id="para" placeholder="Para">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Age of gestation</label>
            <input class="form-control" type="text" name="age_of_gestation"  id="age_of_gestation" placeholder="Age of gestation">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label for="address">Sex</label>
          <select class="form-select" name="gender" id="gender" required>
            <option disabled selected value="">Select gender</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
          </select>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Head Circum</label>
            <input class="form-control" type="text" name="head_circum"  id="head_circum" placeholder="Head Circum">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Chest Circum</label>
            <input class="form-control" type="text" name="chest_circum"  id="chest_circum" placeholder="Chest Circum">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Lenght</label>
            <input class="form-control" type="text" name="length"  id="length" placeholder="Lenght">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Weight</label>
            <input class="form-control" type="text" name="weigth"  id="weigth" placeholder="Weight">
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Date of Discharge</label>
            <input class="form-control" type="date" name="discharge_date"  id="discharge_date" placeholder="Date of Discharge" required>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label>Time of Discharge</label>
            <input class="form-control" type="time" name="discharge_time"  id="discharge_time" placeholder="Time of Discharge" required>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
          <label for="address">Birth Attendant</label>
          <select class="form-select" name="birth_attendant"  id="birth_attendant" required>
            <option selected disabled value="">Choose</option>
                <?php 
                $query = "SELECT * FROM staff WHERE fclt_id = '" . $_SESSION["fcltid"] . "' AND role != 'Admin'";
                  $query_run = mysqli_query($conn, $query);

                  if (mysqli_num_rows($query_run) > 0) {
                    while ($row = mysqli_fetch_assoc($query_run)) {
                ?>
                    <option value="<?= $row['staff_id'] ?>"><?= $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] . ' (' .  $row['role'] . ') '?></option>
                <?php
                    }
                  }
                ?>
          </select>
          </div>
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="birthSave" name="submit">Add Patient</button>
                <button type="submit" class="btn btn-primary d-none" id="birthUpdate" name="submit">Update</button>
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
<script src="js/ajaxInventory.js"></script>

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
                url:'server/search-inventory.php',
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