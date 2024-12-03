<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';
include_once 'includes/staff_functions.inc.php';

$itemsPerPage = 9;

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$staff = getPaginatedStaff($page, $itemsPerPage);

$currentYear = date("Y");
?>

<script src="js/ajaxFunctions.js"></script>
<script src="js/filter.js"></script>
<script src="js/main.js"></script>

<div class="feed">
<div class="head">
    <h2>Staff List</h2> 
  </div>
<div id="yourDivId" class="yourDivClass">
<div class="table-header">
    <div class="col-2">
      <input type="text" name="address" id="address " class="form-control" placeholder="Search">
      </div>
      <div class="col-2">
      <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
      </div>
      <button type="button" class="btn btn-primary" id="report" data-bs-toggle="modal" data-bs-target="#staffModal"><i class="fi fi-br-plus"></i> Add Staff</button>
    </div>
      <div class="prenatal_table">
      <table class="table table-hover" id="staffTable">
        <thead class="table-light">
          <tr>
            <th scope="col" class="px-5">Avatar</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Address</th>
            <th scope="col">Contact No.</th>
            <th scope="col">Role</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Loop through the paginated patients and display each in a table row
          foreach ($staff as $key => $staff) {
            $id = $staff['staff_id'];
            $fname = $staff['fname'];
            $mname = $staff['mname'];
            $lname = $staff['lname'];
            $address = $staff['address'];
            $role = $staff['role'];
            $img = $staff['img'];
            $contact = $staff['contact_num'];
            ?>

            <tr>
            <td class="px-5"><img class="shadow" src="../../assets/<?php echo $img?>" alt=""></td>
            <td><?php echo $fname?></td>
            <td><?php echo $lname?></td>
            <td><?php echo $address?></td>
            <td><?php echo $contact?></td>
            <td><p class="<?php echo $role?>"><?php echo $role?></p></td>
            <td>
          <?php if($role != 'Admin'){
            ?>
            <button type="button" class="btn btn-primary table-btn editStaff" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fi fi-rs-pencil"></i></button>
            <button type="button" class="btn btn-primary table-btn deleteStaff" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
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

<!-- ADD STAFF MODAL -->
<div class="modal fade" id="staffModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Staff's Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body" id="staffModalBody">
        <form id="addStaff">
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
              <label for="fname">First Name</label>
              <input class="form-control" type="text" name="fname" id="fname" placeholder="First Name" value="" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="mname">Middle Name</label>
              <input class="form-control" type="text" name="mname" id="mname" placeholder="Middle Name (optional)">
            </div>
            <div class="mb-3 col-lg-6">
              <label for="lname">Last Name</label>
              <input class="form-control" type="text" name="lname" id="lname" placeholder="Last Name" value="" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="username">Username</label>
              <input class="form-control" type="text" name="username" id="username" placeholder="Username" value="" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="contactNum">Contact Number</label>
              <input class="form-control" type="text" name="contactNum" id="contactNum" placeholder="Contact Number" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="address">Address</label>
              <input class="form-control" type="text" name="address" id="address" placeholder="Address" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="birth_date">Birth Date</label>
              <input class="form-control" type="date" name="birth_date" id="birth_date" placeholder="Birth Date" required>
            </div>
            <div class="mb-3 col-lg-6">
              <label class="form-label">Role</label>
              <select class="form-select" required name="role" id="role">
                <option selected value="">Choose...</option>
                <option value="Nurse">Nurse</option>
                <option value="Midwife">Midwife</option>
                <option value="Doctor">Doctor</option>
              </select>
            </div>
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="cancel">Cancel</button>
        <button type="button" class="btn btn-primary" id="staffsaveButton">Add Staff</button>
        <button type="button" class="btn btn-primary" id="staffeditButton">Edit Staff</button>
        <button type="button" class="btn btn-primary" id="staffupdateButton">Save Edit</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script src="js/ajaxStaff.js"></script>

<?php
include_once 'footer.php'
?>