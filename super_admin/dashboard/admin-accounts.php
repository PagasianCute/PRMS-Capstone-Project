<?php
include_once 'header.php';
include_once 'includes/facilities_functions.inc.php';
$itemsPerPage = 9;

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$facilities = getFacilitiesCard();
$staff = getFacilities($page, $itemsPerPage);

?>

    <div class="main-cards">
        <?php
        $count = 0;
        // Loop through the referrals and display each patient in a table row
        foreach ($facilities as $key => $data) {
          $count++;
          $fclt_name = $data['fclt_name'];
          $staff_count = $data['staff_count'];
          $verification = $data['verification'];
          ?>

          <div class="mini-cards">
            <div class="mini-cards-header">
              <div class="mini-logo">
                <img src="../../assets/<?php echo $verification ?>.png" id="<?php echo $verification ?>" alt="<?php echo $verification ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $verification ?>" >
              </div>
              <h6><?php echo $fclt_name ?></h6>
            </div>
            <div class="mini-card-content">
              <h6 class="mini-name"><?php echo $staff_count ?> Total of Staff</h6>
            </div>
          </div>

          <?php
          }
          if ($count == 0) {
            echo "no records found";
          }
          ?>
      </div>


<div class="feed">
<div id="display-container"></div>
<div class="head">
    <h2>Facility List</h2> 
  </div>
<div id="yourDivId" class="yourDivClass">
<div class="table-header">
    <div class="col-2">
      <input type="text" name="address" id="address " class="form-control" placeholder="Search">
      </div>
      <div class="col-2">
      <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
      </div>
      <button type="button" class="btn btn-primary" id="report" data-bs-toggle="modal" data-bs-target="#staffModal"><i class="fi fi-br-plus"></i> Add Facility</button>
    </div>
      <div class="prenatal_table">
      <table class="table table-hover" id="facilityTable">
        <thead class="table-light">
          <tr>
            <th scope="col" class="px-5">Avatar</th>
            <th scope="col">Facility Name</th>
            <th scope="col">Facility Type</th>
            <th scope="col">Contact No.</th>
            <th scope="col">Province</th>
            <th scope="col">Municipality</th>
            <th scope="col">Verification</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Loop through the paginated patients and display each in a table row
          foreach ($staff as $key => $staff) {
            $fclt_id  = $staff['fclt_id'];
            $fclt_name = $staff['fclt_name'];
            $fclt_type = $staff['fclt_type'];
            $img_url = $staff['img_url'];
            $fclt_contact = $staff['fclt_contact'];
            $province = $staff['province'];
            $municipality = $staff['municipality'];
            $verification = $staff['verification'];
            ?>

            <tr>
            <td class="px-5"><img class="shadow" src="../../assets/<?php echo $img_url?>" alt=""></td>
            <td><?php echo $fclt_name?></td>
            <td><?php echo $fclt_type?></td>
            <td><?php echo $fclt_contact?></td>
            <td><?php echo $province?></td>
            <td><?php echo $municipality?></td>
            <td><p class="<?php echo $verification?>"><?php echo $verification?></p></td>
            <td>
              <button type="button" class="btn btn-primary table-btn editFacility" value="<?php echo $fclt_id?>" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fi fi-rs-pencil"></i></button>
              <button type="button" class="btn btn-primary table-btn deleteFacility" value="<?php echo $fclt_id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
              <?php if($verification == 'Unverified'){
                ?>
                <button type="button" class="btn btn-primary table-btn verifyFacility" value="<?php echo $fclt_id?>" data-toggle="tooltip" data-placement="left" title="Verify"><i class="fi fi-sr-shield-plus"></i></button>
              <?php 
              } ?>
              
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
      $totalPages = ceil(getTotalStaff() / $itemsPerPage);

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
  </div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Filter</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="accordion" id="accordionPanelsStayOpenExample">
    <div class="accordion-item">
      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
          Referred Facilities
        </button>
      </h2>
      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
        <div class="accordion-body">
          <div class="d-grid gap-2 d-md-block">
            <h6>From</h6>
            <div class="facility">
              <input type="checkbox" class="btn-check" id="from-gigaquit-rhu" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="from-gigaquit-rhu">Gigaquit RHU</label>

              <input type="checkbox" class="btn-check" id="from-provencial_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="from-provencial_hospital">Provincial Hospital</label>

              <input type="checkbox" class="btn-check" id="from-caraga_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="from-caraga_hospital">Caraga Hospital</label>

              <input type="checkbox" class="btn-check" id="from-claver_rhu" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="from-claver_rhu">Claver RHU</label>

              <input type="checkbox" class="btn-check" id="from-surigao_provencial_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="from-surigao_provencial_hospital">Surigao Del Norte Provincial Hospital</label>
            </div>
            <h6>To</h6>
            <div class="facility2">
              <input type="checkbox" class="btn-check" id="to-gigaquit-rhu" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="to-gigaquit-rhu">Gigaquit RHU</label>

              <input type="checkbox" class="btn-check" id="to-provencial_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="to-provencial_hospital">Provincial Hospital</label>

              <input type="checkbox" class="btn-check" id="to-caraga_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="to-caraga_hospital">Caraga Hospital</label>

              <input type="checkbox" class="btn-check" id="to-claver_rhu" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="to-claver_rhu">Claver RHU</label>

              <input type="checkbox" class="btn-check" id="to-surigao_provencial_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="to-surigao_provencial_hospital">Surigao Del Norte Provincial Hospital</label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="true" aria-controls="panelsStayOpen-collapseTwo">
          Status
        </button>
      </h2>
      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
        <div class="accordion-body">
          <div class="d-grid gap-2 d-md-block">
            <div class="status">
              <input type="checkbox" class="btn-check" id="Accepted" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="Accepted">Accepted</label>

              <input type="checkbox" class="btn-check" id="Declined" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="Declined">Declined</label>

              <input type="checkbox" class="btn-check" id="Pending" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="Pending">Pending</label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="true" aria-controls="panelsStayOpen-collapseThree">
          Date and Time
        </button>
      </h2>
      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingThree">
          <div class="accordion-body">
            <div class="row">
              <div class="d-grid gap-2 d-md-block">
                <div class="date-time">
                  <input type="checkbox" class="btn-check" id="date" autocomplete="off">
                  <label class="btn btn-outline-secondary btn-sm" for="date" data-bs-toggle="collapse" data-bs-target="#date" aria-expanded="false" aria-controls="date">Date</label>

                  <input type="checkbox" class="btn-check" id="time" autocomplete="off">
                  <label class="btn btn-outline-secondary btn-sm" for="time" data-bs-toggle="collapse" data-bs-target="#time" aria-expanded="false" aria-controls="time">Time</label>
                </div>
              </div>
              <div class="collapse" id="date">
                <div class="col-9 mb-3">
                <input type="date" class="form-control form-control-sm">
                </div>
              </div>
              <div class="collapse" id="time">
                <div class="col-9 mb-3">
                <input type="time" class="form-control form-control-sm">
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>

<!-- ADD Facility MODAL -->
<div class="modal fade" id="staffModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Staff's Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body" id="staffModalBody">
        <form id="addStaff">
          <input type="hidden" name="fclt_id" id="add_fclt_id">
        <div class="image-profile">
            <div class="image-content shadow">
              <img src="../../assets/patient.png" alt="Logo" class="profile-icon" id="staffimagePreview">
            </div>
            <div class="edit-button">
              <button type="button" class="btn btn-primary" id="staffuploadButton">Upload Image</button>
              <input class="form-control d-none" type="file" name="staffformFile" id="staffformFile">
            </div>
          </div>
          <div class="row" style="margin-top: 20px;">
            <div class="mb-3 col-lg-6">
              <label>Referrence ID</label>
              <input class="form-control" type="text" name="fclt_ref_id" id="fclt_ref_id" placeholder="Referrence ID" value="" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label>Facility Name</label>
              <input class="form-control" type="text" name="fclt_name" id="fclt_name" placeholder="Facility Name" value="" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="mname">Facility Type</label>
              <select class="form-select" required name="fclt_type" id="fclt_type">
                <option disabled selected value="">Choose...</option>
                <option value="Birthing Home">Birthing Home</option>
                <option value="Provincial Hospital">Provinical Hospital</option>
                <option value="Hospital">Hospital</option>
              </select>
            </div>
            <div class="mb-3 col-lg-6">
              <label>Contact Number</label>
              <input class="form-control" type="text" name="fclt_contact" id="fclt_contact" placeholder="Contact Number" value="" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label>Rigion</label>
              <select id="region-select" class="form-select" name="region" aria-label="Region Select" required>
                <option selected disabled value="">Select a Region</option>
              </select>
              <input type="text" name="region-name" id="region-name" hidden />
            </div>
            <div class="mb-3 col-lg-6">
              <label>Province</label>
              <select id="province-select" class="form-select" name="province" aria-label="Province Select" required>
                <option selected disabled value="">Select a Province</option>
              </select>
            </div>
            <div class="mb-3 col-lg-6">
              <label>Municipality</label>
              <select id="municipality-select" class="form-select" name="municipality" aria-label="Municipality Select" required>
                <option selected disabled value="">Select a Municipality</option>
              </select>
            </div>
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="staffsaveButton">Add Facility</button>
        <button type="submit" class="btn btn-primary d-none" id="staffupdateButton">Update Facility</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- VERIFY MODAL -->
<div class="modal fade" id="verifyModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Staff's Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
        <div class="modal-body">
            <div id="map" class="mb-3"></div>
            <form id="verificationForm">
              <div class="row">
                <div class="mb-3 col-lg-4">
                  <label>Address</label>
                  <input class="form-control mb-3" type="text" name="address" id="address" placeholder="Municipality" value="" readonly>
                  <input type="hidden" name="fclt_id" id="fclt_id" readonly>
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Latitude</label>
                  <input class="form-control mb-3" type="text" name="latitude" id="latitude" placeholder="Municipality" value="" readonly>
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Longitude</label>
                  <input class="form-control mb-3" type="text" name="longitude" id="longitude" placeholder="Municipality" value="" readonly>
                </div>
              </div>
              <div class="row">
                <h5>Admin Details</h5>
                <div class="mb-3 col-lg-4">
                  <label>First Name</label>
                  <input class="form-control mb-3" type="text" name="fname" id="fname" placeholder="First Name"required>
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Middle Name</label>
                  <input class="form-control mb-3" type="text" name="mname" id="mname" placeholder="Middle Name (Optional)" >
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Last Name</label>
                  <input class="form-control mb-3" type="text" name="lname" id="lname" placeholder="Last Name"required>
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Username</label>
                  <input class="form-control mb-3" type="text" name="username" id="username" placeholder="Username"required>
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Contact Number</label>
                  <input class="form-control mb-3" type="text" name="contact_num" id="contact_num" placeholder="Contact Number"required>
                </div>
                <div class="mb-3 col-lg-4">
                  <label>Birth Date</label>
                  <input class="form-control mb-3" type="date" name="birthdate" id="birthdate"required>
                </div>
              </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Verify</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
<script src="js/ajaxFacilities.js"></script>

<?php
include_once 'footer.php'
?>