<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$itemsPerPage = 12;
$displayreferrals = displayAllReferralTransaction($page, $itemsPerPage);
?>
<div class="feed">
<div class="head" id="reload">
<h2>Referrals</h2>
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
<div class="table-responsive">
    <table class="table equal-width-table" style="text-align: center;">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Referring Unit</th>
          <th scope="col">Name</th>
          <th scope="col" class="action-column">Status</th>
          <th scope="col" class="action-column">Arrival Status</th>
          <th scope="col">Receiving Officer</th>
          <th scope="col">Date • Time</th>
          <th scope="col" class="action-column">Action</th>
        </tr>
      </thead>
      <tbody id="referral-tbody">
        <?php
        $count = 0;
        // Loop through the referrals and display each patient in a table row
        foreach ($displayreferrals as $displayreferral) {
          $count++;
          $rffrl_id = $displayreferral['rfrrl_id'];
          $fclt_name = $displayreferral['fclt_name'];
          $Name = $displayreferral['name'];
          $date = $displayreferral['date'];
          $time = $displayreferral['time'];
          $status = $displayreferral['status'];
          $arrival = $displayreferral['arrival'];
          $patients_id = $displayreferral['patients_id'];
          $staff_name = $displayreferral['lname'] . ', ' . $displayreferral['fname'] . ' ' . $displayreferral['mname'];
          if (empty($displayreferral['lname']) && empty($displayreferral['fname']) && empty($displayreferral['mname'])) {
              $staff_name = "";
          } else {
              $staff_name = $displayreferral['lname'] . ', ' . $displayreferral['fname'] . ' ' . $displayreferral['mname'];
          }
          echo '<tr>
            <th scope="row">' . $count . '</th>
            <td>' . $fclt_name . '</td>
            <td>' . $Name . '</td>
            <td class="action-column" id="'.$status.'-column"><p>' . $status . '</p></td>
            <td class="action-column" id="'.$arrival.'-column"><p>' . $arrival . '</p></td>
            <td>' . $staff_name .'</td>
            <td>' . $date . ' • ' . $time . '</td>
            <td>
            <button class="btn btn-primary table-btn viewRecord" type="button" value="'.$rffrl_id.'"><i class="fi fi-rr-eye"></i></button>
            <button class="btn btn-primary table-btn createNewReferral" type="button" value="'.$patients_id.'"><i class="fi fi-rr-add-folder"></i></button>
            </td>
          </tr>';

        }
        if ($count == 0) {
          echo "no records found";
        }
        ?>
      </tbody>
    </table>
  </div>
  <?php
  // Display pagination controls
  $totalPages = ceil(getTotalReferralsTransaction() / $itemsPerPage);

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

  
<!-- Form Content -->
<div class="modal fade" id="referralModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">From: <span id="fclt_name"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="upperBtn">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Referral Record</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Arrival Information</a>
          </li>
        </ul>
      </div>
      <div class="modal-body">
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <form id="referral_form">
            <div class="row">
              <input type="hidden" name="fclt_id" id="fclt_id" class="form-control">
              <input type="hidden" name="rffrl_id" id="rffrl_id" class="form-control">
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Name</label>
              <input type="text" name="name" id="view_name" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Age</label>
              <input type="text" name="age" id="view_age" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Sex</label>
              <input type="text" name="sex" id="view_sex" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Birth Date</label>
              <input type="date" name="bdate" id="view_bdate" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Address</label>
              <input type="text" name="address" id="view_address" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Admitting Dx</label>
              <input type="text" name="admittingDx" id="view_admittingDx" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Rtpcr</label>
              <input type="text" name="rtpcr" id="view_rtpcr" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Antigen</label>
              <input type="text" name="antigen" id="view_antigen" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Clinical ssx of covid</label>
              <input type="text" name="clinical_ssx" id="view_clinical_ssx" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Exposure to covid</label>
              <input type="text" name="exposure_to_covid" id="view_exposure_to_covid" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Temp</label>
              <input type="text" name="temp" id="view_temp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>HR</label>
              <input type="text" name="hr" id="view_hr" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Resp</label>
              <input type="text" name="resp" id="view_resp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Bp</label>
              <input type="text" name="bp" id="view_bp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>O2sat</label>
              <input type="text" name="02sat" id="view_02sat" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>O2aided</label>
              <input type="text" name="02aided" id="view_02aided" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Procedures needed</label>
              <input type="text" name="procedures_need" id="view_procedures_need" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>FH</label>
              <input type="text" name="fh" id="view_fh" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>IE</label>
              <input type="text" name="ie" id="view_ie" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>FHT</label>
              <input type="text" name="fht" id="view_fht" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>LMP</label>
              <input type="text" name="lmp" id="view_lmp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>EDC</label>
              <input type="text" name="edc" id="view_edc" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>AOG</label>
              <input type="text" name="aog" id="view_aog" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>UTZ</label>
              <input type="text" name="utz" id="view_utz" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>UTZ AOG</label>
              <input type="text" name="utz_aog" id="view_utz_aog" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>EDD</label>
              <input type="text" name="edd" id="view_edd" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Enterpretation</label>
              <input type="text" name="enterpretation" id="view_enterpretation" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Diagnostic test</label>
              <input type="text" name="diagnostic_test" id="view_diagnostic_test" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-6 mb-2">
              <label>Reason For Referral</label>
              <input type="text" name="referral_reason" id="view_view_referral_reason" class="form-control" readonly>
              </div>
          </div>
      
          </div>
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <form id="arrival_form">
              <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
                <label>Status</label>
                <input type="text" name="arrival" id="arrival" class="form-control" readonly>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
                <label>Expected Time</label>
                <input type="text" name="expected_time" id="expected_time" class="form-control" readonly>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
                <label>Patient Status Uppon Arrival</label>
                <input type="text" name="patient_status_upon_arrival" id="patient_status_upon_arrival" class="form-control" readonly>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
                <label>Receiving Officer</label>
                <input type="text" name="receiving_officer" id="receiving_officer" class="form-control" readonly>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
                <label>Arrival Date</label>
                <input type="text" name="arrival_date" id="arrival_date" class="form-control" readonly>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
                <label>Arrival Time</label>
                <input type="text" name="arrival_time" id="arrival_time" class="form-control" readonly>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <div class="referral-audit d-none">
          <div class="mb-3">
            <h5>Referral Audit</h5>
            <div id="referral_transactions"></div>
          </div>
        </div>
      <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary" id="restore_button">Restore Referral</button>
        </div>
        </form>
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
          <form id="createReferral">
            <div class="row">
              <input type="hidden" name="referral-patient-id" id="referral-patient-id" class="form-control" readonly>
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
                <select class="form-select" name="referred_hospital" required>
                <option selected disabled value=""></option>
                <?php 
                $query = "SELECT * FROM facilities WHERE fclt_id != " . $_SESSION["fcltid"] . " AND verification = 'Verified' AND fclt_type != 'Birthing Home'";
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

<script src="js/ajaxAcceptedReferrals.js"></script>
<script src="js/ajaxForReferralsPatients.js"></script>
<script>
    $(document).ready(function(){
        $('#search-input').on("keyup", function(e){
          e.preventDefault();
            var searchTerm = $(this).val();
            $.ajax({
                method:'POST',
                url:'server/search-accepted-referrals.php',
                data:{searchTerm: searchTerm},
                success:function(response)
                {
                    $("#referral-tbody").html(response);
                } 
            });
        });
    });
</script>

<script>
    var fclt_id = "<?php echo $fclt_id ?>";
</script>

<?php
include_once 'footer.php'
?>