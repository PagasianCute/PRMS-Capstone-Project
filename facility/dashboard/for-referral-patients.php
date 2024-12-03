<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';

$itemsPerPage = 12;

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$forReferral = getforReferralPatients($page, $itemsPerPage) ;
?>


<div class="feed">
<div class="head" id="reload">
<h2>For Referral Patients</h2>
</div>
<div id="yourDivId" class="yourDivClass">
 <div class="table-header">
 <div class="col-2">
 <input type="text" id="search-input" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-search">
  </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary filterBtn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
  </div>
 </div>
 <div class="table-responsive prenatal_table">
  <table id="referralsTable" class="table" style="text-align: center;">
    <thead class="table-light">
      <tr>
        <th scope="col">First Name</th>
        <th scope="col">Middle Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Referred Facility</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">Health Provider Name</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody id="referral-tbody">
      <?php
      $count = 0;
      // Loop through the referrals and display each patient in a table row
      foreach ($forReferral as $key => $data) {
        $count++;
        $id = $data['id'];
        $patient_fname = $data['patient_fname'];
        $patient_mname = $data['patient_mname'];
        $patient_lname = $data['patient_lname'];
        $staff_name = $data['staff_lname'] . ', ' . $data['staff_fname'] . ' ' . $data['staff_mname'];
        $date = $data['date'];
        $record = $data['fclt_name'];
        $timeString = $data['time'];
        $patients_id = $data['patients_id'];

        $timeAmPm = 'Invalid Time';

        if (!empty($timeString)) {
            $time = DateTime::createFromFormat('H:i:s', $timeString);

            // Check if the $time is valid
            if ($time !== false) {
                $timeAmPm = $time->format('h:i A');
            }
        }
        ?>
        <tr>
          <td><?php echo $patient_fname ?></td>
          <td><?php echo $patient_mname ?></td>
          <td><?php echo $patient_lname ?></td>
          <td><?php echo $record ?></td>
          <td><?php echo $date ?></td>
          <td><?php echo $timeAmPm ?></td>
          <td><?php echo $staff_name ?></td>
          <td>
          <button class="btn btn-primary table-btn createNewReferral" data-toggle="tooltip" data-placement="left" title="Add Record" value="<?php echo $id?>">
            <i class="fi fi-rr-add-folder"></i>
          </button>
            <button type="button" class="btn btn-primary table-btn updatePatientReferral" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
          </td>
        </tr>
      <?php
      }
      if ($count == 0) {
        echo "<tr><td colspan='8'>No records found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

  <?php
  // Display pagination controls
  $totalPages = ceil(getTotalForReferralPatients() / $itemsPerPage);

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
            <div class="facility">
              
              <input type="checkbox" class="btn-check" id="caraga_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="caraga_hospital">Caraga Regional Hospital</label>

              <input type="checkbox" class="btn-check" id="surigao_provencial_hospital" autocomplete="off">
              <label class="btn btn-outline-secondary btn-sm" for="surigao_provencial_hospital">Surigao Del Norte Provincial Hospital</label>
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
   
<!-- CREATE REFERRAL -->
<div class="modal fade" id="createReferralModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true" aria-labelledby="referralModalToggleLabel" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content new_modal">
      <div class="modal-header">
      <h2 class="modal-title" id="createReferralModalLabel">Create Referral</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="upperBtn">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="referral_create-tab" data-bs-toggle="tab" href="#referral_create" role="tab" aria-controls="referral_create" aria-selected="true">Referral Record</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="patientInformation_create-tab" data-bs-toggle="tab" href="#patientInformation_create" role="tab" aria-controls="patientInformation_create" aria-selected="false">Patient Information</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="birthExperience_create-tab" data-bs-toggle="tab" href="#birthExperience_create" role="tab" aria-controls="birthExperience_create" aria-selected="false">Birth Experience</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="trimesters_create-tab" data-bs-toggle="tab" href="#trimesters_create" role="tab" aria-controls="trimesters_create" aria-selected="false">Trimester Records</a>
          </li>
          <li class="nav-item">
            <select class="nav-link" name="recordsCount_referralCreate" id="referralRecordsCount_referralCreate">
                <!-- PATIENT RECORDS DISPLAY HERE FROM JS  -->
            </select>
          </li>
        </ul>
      </div>
      <div class="modal-body">
        <div class="tab-content" id="myTabContent3">
          <div class="tab-pane fade show active" id="referral_create" role="tabpanel" aria-labelledby="referral_create-tab">
            <div class="alert alert-danger d-none" id="referralError"></div>
          <form id="createReferralForReferralPatient">
            <div class="row">
              <input type="hidden" name="referral-patient-id" id="referral-patient-id" class="form-control" readonly>
              <input type="hidden" name="referral-patient-record" id="referral-patient-record" class="form-control" readonly>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Name</label>
              <input type="text" name="name" id="name" class="form-control" required>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Age</label>
              <input type="number" name="age" id="age" class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Sex</label>
              <select class="form-select" name="sex" id="sex" aria-label="Default select example" style="height: 38px; margin:auto; padding:5px 10px;" required>
                <option disabled selected value="">Select</option>
                <option value="Female">Female</option>
                <option value="Male">Male</option>
              </select>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Birth Date</label>
              <input type="date" name="bdate" id="bdate" class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Barangay</label>
              <input type="text" name="address" id="address" class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Admitting Dx</label>
              <input type="text" name="admitting_dx" id="admitting_dx " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Rtpcr</label>
              <input type="text" name="rtpcr" id="rtpcr " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Antigen</label>
              <input type="text" name="antigen" id="antigen " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Clinical ssx of covid</label>
              <input type="text" name="clinical_ssx" id="clinical_ssx " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Exposure to covid</label>
              <input type="text" name="exposure_to_covid" id="exposure_to_covid " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Temp</label>
              <input type="text" name="temp" id="temp " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>HR</label>
              <input type="text" name="hr" id="hr " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Resp</label>
              <input type="text" name="resp" id="resp " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Bp</label>
              <input type="text" name="bp" id="bp " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>O2sat</label>
              <input type="text" name="O2sat" id="02sat " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>O2aided</label>
              <input type="text" name="O2aided" id="02aided " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Procedures needed</label>
              <input type="text" name="procedures_need" id="procedures_need " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>FH</label>
              <input type="text" name="fh" id="fh " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>IE</label>
              <input type="text" name="ie" id="ie " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>FHT</label>
              <input type="text" name="fht" id="fht " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>LMP</label>
              <input type="text" name="lmp" id="lmp " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>EDC</label>
              <input type="text" name="edc" id="edc " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>AOG</label>
              <input type="text" name="aog" id="aog " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>UTZ</label>
              <input type="text" name="utz" id="utz " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>UTZ AOG</label>
              <input type="text" name="utz_aog" id="utz_aog " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>EDD</label>
              <input type="text" name="edd" id="edd " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Enterpretation</label>
              <input type="text" name="enterpretation" id="enterpretation " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Diagnostic test</label>
              <input type="text" name="diagnostic_test" id="diagnostic_test " class="form-control">
              </div>
          </div>
          <div class="row">
              <div class="col-3 otherDiv d-none">
              <label>Reason For Referral (Other)</label>
              <input type="text" name="other" id="other" class="form-control" placeholder="Other">
              </div>
              <div class="col-3">
                <label>Select Refer Hospital</label>
                <div class="col-lg-12 col-md-6 col-sm-12">
                <select class="form-select" name="referred_hospital" id="referred_hospital" required>
                <option selected disabled value=""></option>
                <?php 
                $query = "SELECT * FROM facilities WHERE fclt_id != '" . $_SESSION["fcltid"] . "' AND verification = 'Verified'";
                  $query_run = mysqli_query($conn, $query);

                  if (mysqli_num_rows($query_run) > 0) {
                    while ($row = mysqli_fetch_assoc($query_run)) {
                ?>
                    <option value="<?= $row['fclt_id'] ?>"><?= $row['fclt_name'] ?></option>
                <?php
                    }
                  }
                ?>
              </select>
              </div>
              </div>
              <div class="col-3">
                <label>Emergency Type</label>
                <div class="col-lg-12 col-md-6 col-sm-12">
                <select class="form-select" name="emergency_type" required>
                <option selected disabled value="">Please choose</option>
                <option value="Urgent">Urgent (High Priority)</option>
                <option value="Routine">Routine (Normal)</option>
              </select>
              </div>
              </div>
              <div class="col-6">
                <label>Reason For Referral</label>
                <div class="col-lg-12 col-md-12 col-sm-12">
                <select class="form-select mb-3" name="referral_reason" id="referral_reason" required>
                <option selected disabled value=""></option>
                <?php 
                $query = "SELECT * FROM ap_risk_codes";
                  $query_run = mysqli_query($conn, $query);

                  if (mysqli_num_rows($query_run) > 0) {
                    while ($row = mysqli_fetch_assoc($query_run)) {
                ?>
                    <option value="<?= $row['title'] , ' (Code ', $row['code'],')'?>"><?= $row['title'] , ' (Code ', $row['code'],')'?></option>
                <?php
                    }
                  }
                ?>
                <option value="Other">Other</option>
              </select>
              </div>
              </div>
          </div>
          </div>
          <div class="tab-pane fade" id="patientInformation_create" role="tabpanel" aria-labelledby="patientInformation_create-tab">
            <?php include 'patient_information_create.php' ?>
          </div>
          <div class="tab-pane fade" id="birthExperience_create" role="tabpanel" aria-labelledby="birthExperience_create-tab">
            <?php include 'patient_birth_experience_create.php' ?>
          </div>
          <div class="tab-pane fade" id="trimesters_create" role="tabpanel" aria-labelledby="trimesters_create-tab">
            <?php include 'record_referral_create.php' ?>
            <div class="noRecords">
                  <h1>No Trimester Records from The System</h1>
                </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary" id="submitButton">Create</button>
      <button class="btn btn-primary d-none" type="button" disabled id="loadingButton">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...
      </button>
        </form>
      </div>
    </div>
  </div>
</div>


<script src="js/ajaxForReferralsPatients.js"></script>
<script src="js/filter.js"></script>

<script>
    $(document).ready(function(){
        $('#search-input').on("keyup", function(e){
          e.preventDefault();
            var searchTerm = $(this).val();
            $.ajax({
                method:'POST',
                url:'server/search-my-referral-list.php',
                data:{searchTerm: searchTerm},
                success:function(response)
                {
                    $("#referral-tbody").html(response);
                } 
            });
        });
    });
</script>

<?php
include_once 'footer.php'
?>