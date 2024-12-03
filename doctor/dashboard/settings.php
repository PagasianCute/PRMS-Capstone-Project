<?php
include_once 'header.php';

include_once 'includes/referral_functions.inc.php';
include_once 'includes/staff_functions.inc.php';

$itemsPerPage = 9;

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$staff = getPaginatedStaff($page, $itemsPerPage);
?>

<!-- Referral Forms -->
<div class="feed">
<div class="head"><h2>Settings</h2></div>

<ul class="nav nav-underline" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Facility Profile</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Staff List</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Contact</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
    <div class="card mb-3 border-0 facility-profile">
      <form id="editFacility">
          <div class="row g-0">
              <div class="col-md-3">
                  <img src="../dashboard/assets/<?php echo $_SESSION["fcltimg"] ?>" class="img-fluid" alt="facility image">
              </div>
              <div class="col-md-8">
                  <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                          <label for="fname">Facility ID</label>
                          <input class="form-control" type="text" name="fclt_id" id="fclt_id" value="<?php echo $_SESSION["fcltuid"] ?>" readonly>
                        </div>
                        <div class="mb-3 col-lg-6">
                          <label for="mname">Facility Name</label>
                          <input class="form-control" type="text" name="fclt_name" id="fclt_name" value="<?php echo $_SESSION["fcltname"] ?>" readonly>
                        </div>
                        <div class="mb-3 col-lg-6">
                          <label for="lname">Address</label>
                          <input class="form-control" type="text" name="fclt_address" id="fclt_address" value="<?php echo $_SESSION["fcltaddress"] ?>" readonly>
                        </div>
                        <div class="mb-3 col-lg-6">
                          <label for="contactNum">Contact Number</label>
                          <input class="form-control" type="text" name="fclt_contact" id="fclt_contact" value="<?php echo $_SESSION["fcltcontact"] ?>" readonly>
                        </div>
                      </div>
                      <button type="button" class="btn btn-primary" id="facilityEditButton">Edit</button>
                      <button type="button" class="btn btn-primary d-none" id="cancel">Cancel</button>
                      <button type="button" class="btn btn-primary  d-none" id="facilitySaveButton">Save</button>
                  </div>
              </div>
          </div>
        </form>
      </div>
  </div>
  <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
    <div class="table-header">
    <div class="col-2">
      <input type="text" name="address" id="address " class="form-control" placeholder="Search">
      </div>
      <div class="col-2">
      <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
      </div>
      <button type="button" class="btn btn-primary" id="report"><i class="fi fi-rr-upload"></i> Export</button>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staffModal"><i class="fi fi-br-plus"></i> Add Staff</button>
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
            <td class="px-5"><img class="shadow" src="../dashboard/assets/<?php echo $img?>" alt=""></td>
            <td><?php echo $fname?></td>
            <td><?php echo $lname?></td>
            <td><?php echo $address?></td>
            <td><?php echo $contact?></td>
            <td><p class="<?php echo $role?>"><?php echo $role?></p></td>
            <td>
              <button type="button" class="btn btn-primary table-btn editStaff" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fi fi-rs-pencil"></i></button>
              <button type="button" class="btn btn-primary table-btn deleteStaff" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
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
  <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">...</div>
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
              <img src="../dashboard/assets/patient.png" alt="Logo" class="profile-icon" id="staffimagePreview">
            </div>
            <div class="edit-button">
              <button type="button" class="btn btn-primary" id="staffuploadButton">Upload Image</button>
              <input class="form-control d-none" type="file" name="staffformFile" id="staffformFile">
            </div>
          </div>
          <div class="row" style="margin-top: 20px;">
            <div class="mb-3 col-lg-6">
              <label for="fname">First Name</label>
              <input class="form-control" type="text" name="fname" id="fname" placeholder="First Name" required>
              <div class="invalid-feedback">
                Please enter first name.
              </div>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="mname">Middle Name</label>
              <input class="form-control" type="text" name="mname" id="mname" placeholder="Middle Name (optional)">
            </div>
            <div class="mb-3 col-lg-6">
              <label for="lname">Last Name</label>
              <input class="form-control" type="text" name="lname" id="lname" placeholder="Last Name" required>
              <div class="invalid-feedback">
                Please enter last name.
              </div>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="contactNum">Contact Number</label>
              <input class="form-control" type="text" name="contactNum" id="contactNum" placeholder="Contact Number" required>
              <div class="invalid-feedback">
                Please enter contact number.
              </div>
            </div>
            <div class="mb-3 col-lg-6">
              <label for="address">Address</label>
              <input class="form-control" type="text" name="address" id="address" placeholder="Address" required>
              <div class="invalid-feedback">
                Please enter address.
              </div>
            </div>
            <div class="mb-3 col-lg-6">
              <label class="form-label">Role</label>
              <select class="form-select" required name="role" id="role">
                <option selected disabled value="">Choose...</option>
                <option value="Nurse">Nurse</option>
                <option value="Midwife">Midwife</option>
                <option value="Doctor">Doctor</option>
              </select>
              <div class="invalid-feedback">
                Please enter role.
              </div>
            </div>
          </div>
          <div class="alert alert-danger d-none" id="errorMessage"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="staffsaveButton">Add Staff</button>
      </div>
      </form>
    </div>
  </div>
</div>


<script src="js/ajaxStaff.js"></script>
<?php
require 'footer.php';
?>
