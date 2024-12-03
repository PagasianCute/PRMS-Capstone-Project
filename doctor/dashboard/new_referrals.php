<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';

$usersName = $_SESSION["usersname"];
$fclt_muinicipality = $_SESSION["fcltmunicipality"];
// Call the function and fetch all the referrals
$ProvHos = ProvHosPendingReferrals();
$Hospital = HospitalPendingReferrals();
$arrival = newArrival();
$getreferral = getAllReferrals();
?>

<link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
      integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
      crossorigin=""
    />
<!-- NEW REFERRALS -->
<div class="container-fuild">
  <div class="row gx-3">
    <div class="col-lg-6 col-md-12 mb-4">
    <div class="feed">
    <div class="head" id="reload">
        <h3 class="left-heading mb-4">New Referrals</h3>
    </div>
    <div class="newReferrals">
        <?php
        $count = 0;

        if ($_SESSION["fclttype"] == 'Hospital') {
            $dataArray = $Hospital;
        } elseif ($_SESSION["fclttype"] == 'Provincial Hospital') {
            $dataArray = $ProvHos;
        } else {
            $dataArray = array(); // Handle other cases or provide a default value
        }

        // Loop through the referrals and display each patient in a table row
        foreach ($dataArray as $data) {
            $rffrl_id = $data['rfrrl_id'];
            $Fromfclt_name = $data['from_facility_name'];
            $Referredfclt_name = $data['referred_facility_name'];
            $Name = $data['name'];
            $time = $data['sent_time'];
            $date = $data['sent_date'];
            $staff_name = $data['staffs_name'];
            $formattedTime = date("h:i A", strtotime($time));
            $count++;
            ?>
            
            <div class="card mb-3">
              <div class="row g-0">
                <div class="col-md-5" style="padding: 15px;">
                  <img src="../../assets/facility.jpg" class="img-fluid rounded" alt="...">
                </div>
                <div class="col-md-7">
                  <div class="card-body">
                    <h5 class="card-title">From: <?php echo $Fromfclt_name ?></h5>
                    <p class="card-text mb-1"> <span class="fw-bold">Patient's Name: </span><?php echo $Name ?></p>
                    <p class="card-text mb-1"> <span class="fw-bold">Sent by: </span><?php echo  $staff_name ?></p>
                    <p class="card-text mb-1"> <span class="fw-bold">Sent Date: </span><?php echo $date ?></p>
                    <p class="card-text mb-2"> <span class="fw-bold">Sent Time: </span><?php echo $formattedTime ?></p>
                    <p class="card-text mb-1"><button type="button" value="<?php echo $rffrl_id ?>" class="viewRecord btn btn-primary">View Referral</button></p>
                  </div>
                </div>
              </div>
            </div>
            <?php
        }

        if ($count == 0) {
            echo "No New Referrals";
        }
        ?>
        </div>
    </div>
    </div>

    <!-- NEW ARRIVAL -->
  <div class="col-lg-6 col-md-12 mb-4">
    <div class="feed">
        <div class="head" id="reload">
            <h3 class="left-heading mb-4">Patient Arrival</h3>
        </div>
        <div id="newArrivals" class="newArrivals">
        <?php
        $count = 0;

        if ($_SESSION["fclttype"] == 'Hospital') {
            $dataArray = $Hospital;
        } elseif ($_SESSION["fclttype"] == 'Provincial Hospital') {
            $dataArray = $ProvHos;
        } else {
            $dataArray = array(); // Handle other cases or provide a default value
        }

        // Loop through the referrals and display each patient in a table row
        foreach ($arrival as $data) {
          $rffrl_id = $data['rfrrl_id'];
          $fclt_name = $data['fclt_name'];
          $Name = $data['name'];
          $time = $data['expected_arrival'];
          $arrival_time = $data['arrival_time'];
          $arrival = $data['arrival'];
          $travel_time = $data['travel_time'];
          $formattedTime = date("h:i A", strtotime($time));
          $formattedArrivalTime = date("h:i A", strtotime($arrival_time));
          $count++;

          if ($arrival == 'Arrived') {
            $arrived = '<span class="fw-bold">Time Arrived: </span>' . $formattedArrivalTime;
            } else {
                $arrived = '';
            }          
          ?>
          
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-5" style="padding: 15px;">
                <img src="../../assets/facility.jpg" class="img-fluid rounded" alt="...">
              </div>
              <div class="col-md-7">
                <div class="card-body">
                  <h5 class="card-title">From: <?php echo $fclt_name ?></h5>
                  <p class="card-text mb-1"> <span class="fw-bold">Name: </span><?php echo $Name ?></p>
                  <p class="card-text mb-1"> <span class="fw-bold">Status: </span><?php echo $arrival ?></p>
                  <p class="card-text mb-1"> <span class="fw-bold">Travel TIme: </span><?php echo $travel_time ?></p>
                  <p class="card-text mb-2"> <span class="fw-bold">Expected Time: </span><?php echo $formattedTime ?></p>
                  <p class="card-text mb-2"> <?php echo $arrived ?></p>
                  <div class="card-text mb-2 d-flex" style="gap: 10px;">
                 </div>
                </div>
              </div>
            </div>
          </div>
          <?php
      }

        if ($count == 0) {
            echo "No New Arrival";
        }
        ?>
        </div>
      </div>
    </div>
    </div>
</div>
  
<!-- VIEW NEW REFERRALS -->
<div class="modal fade" id="referralModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <div class="conducting-person">
            <img id="conducting_person_img" src="" alt="">
            <h5 class="modal-title mb-2">Conducting Person: <span id="conducting_person"></span></h5>
          </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <h5 class="modal-title fclt-title">From: <span id="fclt_name"></span></h5>
        <div class="upperBtn">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active" id="referralRecord-tab" data-bs-toggle="tab" href="#referralRecord" role="tab" aria-controls="referralRecord" aria-selected="true">Referral Record</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="patientInformation-tab" data-bs-toggle="tab" href="#patientInformation" role="tab" aria-controls="patientInformation" aria-selected="false">Patient Information</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="birthExperience-tab" data-bs-toggle="tab" href="#birthExperience" role="tab" aria-controls="birthExperience" aria-selected="false">Birth Experience</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="trimesters-tab" data-bs-toggle="tab" href="#trimesters" role="tab" aria-controls="trimesters" aria-selected="false">Trimester Records</a>
            </li>
            <li class="nav-item">
              <select class="nav-link" name="recordsCount" id="referralRecordsCount">
              <!-- PATIENT RECORDS DISPLAY HERE FROM JS  -->
              </select>
            </li>
          </ul>
      </div>
      <div class="modal-body">
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="referralRecord" role="tabpanel" aria-labelledby="referralRecord-tab">
            <form id="referral_form">
              <div class="row">
              <input type="hidden" name="fclt_id" id="fclt_id" class="form-control">
              <input type="hidden" name="rffrl_id" id="rffrl_id" class="form-control">
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Name</label>
              <input type="text" name="name" id="name" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Age</label>
              <input type="text" name="age" id="age" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Sex</label>
              <input type="text" name="sex" id="sex" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Birth Date</label>
              <input type="date" name="bdate" id="bdate" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Address</label>
              <input type="text" name="address" id="address" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Admitting Dx</label>
              <input type="text" name="admittingDx" id="admittingDx" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Rtpcr</label>
              <input type="text" name="rtpcr" id="rtpcr" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Antigen</label>
              <input type="text" name="antigen" id="antigen" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Clinical ssx of covid</label>
              <input type="text" name="clinical_ssx" id="clinical_ssx" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Exposure to covid</label>
              <input type="text" name="exposure_to_covid" id="exposure_to_covid" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Temp</label>
              <input type="text" name="temp" id="temp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>HR</label>
              <input type="text" name="hr" id="hr" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Resp</label>
              <input type="text" name="resp" id="resp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Bp</label>
              <input type="text" name="bp" id="bp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>O2sat</label>
              <input type="text" name="02sat" id="02sat" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>O2aided</label>
              <input type="text" name="02aided" id="02aided" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Procedures needed</label>
              <input type="text" name="procedures_need" id="procedures_need" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>FH</label>
              <input type="text" name="fh" id="fh" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>IE</label>
              <input type="text" name="ie" id="ie" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>FHT</label>
              <input type="text" name="fht" id="fht" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>LMP</label>
              <input type="text" name="lmp" id="lmp" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>EDC</label>
              <input type="text" name="edc" id="edc" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>AOG</label>
              <input type="text" name="aog" id="aog" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>UTZ</label>
              <input type="text" name="utz" id="utz" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>UTZ AOG</label>
              <input type="text" name="utz_aog" id="utz_aog" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>EDD</label>
              <input type="text" name="edd" id="edd" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Enterpretation</label>
              <input type="text" name="enterpretation" id="enterpretation" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
              <label>Diagnostic test</label>
              <input type="text" name="diagnostic_test" id="diagnostic_test" class="form-control" readonly>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-6 mb-2">
              <label>Reason For Referral</label>
              <input type="text" name="referral_reason" id="view_referral_reason" class="form-control" readonly>
              </div>
              <input type="hidden" id="address1">
              <input type="hidden" id="address2" value="<?php echo $fclt_muinicipality ?>">
              <input type="hidden" id="expectedTime" name="expectedTime">
          </div>
      
          </div>
            <div class="tab-pane fade" id="patientInformation" role="tabpanel" aria-labelledby="patientInformation-tab">
              <?php include 'patient_information.php' ?>
            </div>
            <div class="tab-pane fade" id="birthExperience" role="tabpanel" aria-labelledby="birthExperience-tab"> 
              <?php include 'patient_birth_experience.php' ?>
            </div>
            <div class="tab-pane fade" id="trimesters" role="tabpanel" aria-labelledby="trimesters-tab">
              <?php include 'record.php' ?>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <div class="referral-audit d-none">
            <div id="referral_transactions"></div>
        </div>
        <div class="referral-reason">
          <div class="mb-3">
            <h5>Decline Reason</h5>
            <div class="alert alert-danger d-none" id="errorMessage"></div>
            <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
          </div>
        </div>
        </form>
        <div class="referral-buttons">
          <button type="button" class="btn close" id="decline_referral">Decline Referral</button>
          <button type="button" class="btn close" id="cancel_button">Cancel</button>
          <button type="button" class="btn btn-primary" id="decline_button">
          <span class="decline-loading d-none"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Declining Referral...</span>
          <span class="decline-span">Decline Referral</span>
          </button>
          <button type="button" class="btn btn-primary" id="accept_button">
          <span class="accept-loading d-none"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Accepting Referral...</span>
          <span class="travel-loading d-none"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Computing Travel time...</span>
          <span class="accept-span">Accept Referral</span>
          </button>
          <button type="button" data-bs-dismiss="modal" id="viewBtnClose" class="btn close">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- VERIFY MODAL -->
<div class="modal fade" id="TravelTime" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Map</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
        <div class="modal-body">
            <div id="map" class="mb-3"></div>
        <div class="modal-footer">
          <div class="arriving-footer">
            <div class="arriving-details">
              <h5>Travel Time: <span id="travel-time-span"></span></h5>
              <h5>Extected Time: <span id="expected-time-span"></span></h5>
            </div>
            <button type="button" class="btn close" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- VIEW ARRIVAL REFERRALS -->
<div class="modal fade" id="arrivalModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">Patient Arrival</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
          <div class="modal-body">
            <form id="arrival_form">
              <div class="row">
                <div class="col-lg-12 mb-2">
                  <h6>From: <span id="view_fclt_name"></span></h6>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Status of Patient Upon Arrival</label>
                <textarea type="text" name="patient_status_upon_arrival" id="patient_status_upon_arrival" class="form-control" required></textarea>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Receiving Officer</label>
                <input type="text" name="receiving_officer" id="receiving_officer" class="form-control" value="<?php echo $usersName ?>" required>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Date Received</label>
                <input type="date" name="arrival_date" id="arrival_date" class="form-control" required>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Time Received</label>
                <input type="time" name="arrival_time" id="arrival_time" class="form-control" required>
                </div>
              </div>
          </div>
      <div class="modal-footer">
        <div class="referral-buttons">
          <button type="button" data-bs-dismiss="modal" class="btn close">Close</button>
          <button type="submit" class="btn btn-primary patientArrivedSubmit">
          <span class="arrival-loading d-none"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting Arrival...</span>
          <span class="arrival-span">Submit</span>
          </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    var fclt_id = "<?php echo $fclt_id ?>";
    var fclt_type = "<?php echo $_SESSION["fclttype"] ?>";
    var fclt_muinicipality = "<?php echo $_SESSION["fcltmunicipality"] ?>";
    var address2 = "<?php echo $_SESSION["fcltmunicipality"] ?>";
</script>
<script
      src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
      integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
      crossorigin=""
    ></script>

<script src="js/ajaxNewReferrals.js"></script>

<?php
include_once 'footer.php'
?>