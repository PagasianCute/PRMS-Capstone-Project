<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';

$usersName = $_SESSION["usersname"];
$fclt_id = $_SESSION["fcltid"];
$staff_id = $_SESSION["usersid"];
// Call the function and fetch all the referrals
$ProvHos = ProvHosPendingReferrals();
$Hospital = HospitalPendingReferrals();
$arrival = newArrival();
$getreferral = getAllReferrals();
?>
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
            $time = $data['time'];
            $date = $data['date'];
            $status = $data['status'];
            $formattedTime = date("h:i A", strtotime($time));
            $count++;
            ?>
            
            <div class="card mb-3 ">
              <div class="row g-0">
                <div class="col-md-5" style="padding: 15px;">
                  <img src="../../assets/facility.jpg" class="img-fluid rounded" alt="...">
                </div>
                <div class="col-md-7">
                  <div class="card-body">
                    <h5 class="card-title">From: <?php echo $Fromfclt_name ?></h5>
                    <p class="card-text mb-1"> <span class="fw-bold">Referred Hospital: </span><?php echo $Referredfclt_name?></p>
                    <p class="card-text mb-1"> <span class="fw-bold">Status: </span><?php echo  $status ?></p>
                    <p class="card-text mb-1"> <span class="fw-bold">Referral ID: </span><?php echo $rffrl_id ?></p>
                    <p class="card-text mb-1"> <span class="fw-bold">Date: </span><?php echo $date ?></p>
                    <p class="card-text mb-2"> <span class="fw-bold">Time: </span><?php echo $formattedTime ?></p>
                    <div class="card-text mb-2 d-flex" style="gap: 10px;">
                    <p class="card-text mb-1"><button type="button" value="<?php echo $rffrl_id ?>" class="viewRecord btn btn-primary">View Referral</button></p>
                    <p class="card-text mb-1"><button type="button" value="<?php echo $rffrl_id ?>" class="sendToDoctor btn btn-primary">Send To Doctor</button></p>
                    </div>
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
            <h3 class="left-heading mb-4">New Arrival</h3>
        </div>
        <div class="newPatientArrival">
        <?php
        $count = 0;

        // Loop through the referrals and display each patient in a table row
        foreach ($arrival as $data) {
          $rffrl_id = $data['rfrrl_id'];
          $fclt_name = $data['fclt_name'];
          $Name = $data['name'];
          $time = $data['expected_arrival'];
          $arrival = $data['arrival'];
          $travel_time = $data['travel_time'];
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
                  <h5 class="card-title">From: <?php echo $fclt_name ?></h5>
                  <p class="card-text mb-1"> <span class="fw-bold">Referral ID: </span><?php echo $rffrl_id ?></p>
                  <p class="card-text mb-1"> <span class="fw-bold">Status: </span><?php echo $arrival ?></p>
                  <p class="card-text mb-1"> <span class="fw-bold">Travel TIme: </span><?php echo $travel_time ?></p>
                  <p class="card-text mb-2"> <span class="fw-bold">Expected Time: </span><?php echo $formattedTime ?></p>
                  <div class="card-text mb-2 d-flex" style="gap: 10px;">
                  <p class="card-text mb-1"><button type="button" class="btn btn-primary patientArrived" value="<?php echo $rffrl_id ?>">Patient Arrival</button></p>
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
  
  <div class="add-button-container">
        <button class="add-button" id="referrals-btn" value="<?php echo $staff_id ?>"><i class="fi fi-rr-comment"></i></button>
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
      <div class="modal-body">
            <form id="referral_form">
              <div class="row">
              <h5 class="modal-title fclt-title mb-2">From: <span id="fclt_name"></span></h5>
              <span class="border-bottom mb-3"></span>
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
              <input type="text" name="bdate" id="bdate" class="form-control" readonly>
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
          </div>
      </div>
      <div class="modal-footer">
        <div class="referral-audit d-none">
          <div>
            <h5>Referral Audit</h5>
            <div id="referral_transactions"></div>
          </div>
        </div>
        </form>
        <div class="referral-buttons">
          <button type="button" data-bs-dismiss="modal" id="viewBtnClose" class="btn close">Close</button>
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
                <label>Status of Patient Upon Arrival</label>
                <textarea type="text" name="patient_status_upon_arrival" id="patient_status_upon_arrival" class="form-control" required></textarea>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Receiving Officer</label>
                <input type="text" name="receiving_officer" id="receiving_officer" class="form-control" value="<?php echo $usersName ?>" readonly>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Date Received</label>
                <input type="date" name="arrival_date" id="arrival_date" class="form-control" readonly>
                </div>
                <div class="col-lg-12 mb-2">
                <label>Time Received</label>
                <input type="time" name="arrival_time" id="arrival_time" class="form-control" readonly>
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

<!-- SEND TO DOCTOR REFERRALS -->
<div class="modal fade" id="send_to_doctor_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">Submit Referral</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="send_to_doctor_form">
          <div class="modal-body">
              <div class="row">
                <div class="col-lg-12 mb-2">
                  <label for="validationDefault04" class="form-label">Send To Dr.</label>
                  <input type="hidden" name="staff_id" id="staff_id" value="<?php echo $staff_id ?>">
                  <select class="form-select" name="doctor_id" id="doctor_id" required>
                    <option selected disabled value="">Choose...</option>
                    <?php 
                      $query = "SELECT * FROM staff WHERE role = 'Doctor' AND fclt_id = $fclt_id";
                        $query_run = mysqli_query($conn, $query);

                        if (mysqli_num_rows($query_run) > 0) {
                          while ($row = mysqli_fetch_assoc($query_run)) {
                      ?>
                          <option value="<?= $row['staff_id']?>"><?= 'Dr. '. $row['fname'] . ' '. $row['mname'].' ' . $row['lname']?></option>
                      <?php
                          }
                        }
                      ?>
                  </select>
                </div>
              </div>
          </div>
      <div class="modal-footer">
        <div class="referral-buttons">
          <button type="button" data-bs-dismiss="modal" class="btn close">Close</button>
          <button type="submit" class="btn btn-primary patientArrivedSubmit">
          <span class="arrival-loading d-none"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting to Doctor...</span>
          <span class="arrival-span">Submit</span>
          </button>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- VIEW ARRIVAL REFERRALS -->
<div class="modal fade" id="edit_send_to_doctor_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">Referral ID: <span id="doctor-referral-span"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="update_to_doctor_form">
              <div class="row">
                <div class="col-lg-12 mb-2">
                  <label for="validationDefault04" class="form-label">Send To Dr.</label>
                  <input type="hidden" name="doctors_referral_id" id="doctors_referral_id">
                  <select class="form-select" name="doctor_id" required>
                    <option selected disabled value="">Choose...</option>
                    <?php 
                      $query = "SELECT * FROM staff WHERE role = 'Doctor' AND fclt_id = $fclt_id";
                        $query_run = mysqli_query($conn, $query);

                        if (mysqli_num_rows($query_run) > 0) {
                          while ($row = mysqli_fetch_assoc($query_run)) {
                      ?>
                          <option value="<?= $row['staff_id']?>"><?= 'Dr. '. $row['fname'] . ' '. $row['mname'].' ' . $row['lname']?></option>
                      <?php
                          }
                        }
                      ?>
                  </select>
                </div>
              </div>
          </div>
      <div class="modal-footer">
        <div class="referral-buttons">
          <button type="button" data-bs-target="#all_sended_referrals" data-bs-toggle="modal" class="btn close">Back</button>
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


<!-- SEND TO DOCTOR REFERRALS -->
<div class="modal fade" id="all_sended_referrals" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">Sended Referrals</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
          <div class="modal-body">

          </div>
      <div class="modal-footer">
        <div class="referral-buttons">
          <button type="button" data-bs-dismiss="modal" class="btn close">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    var fclt_id = "<?php echo $fclt_id ?>";
    var fclt_type = "<?php echo $_SESSION["fclttype"] ?>"
    var user_id = "<?php echo $_SESSION["usersid"]; ?>"
</script>

<script src="js/ajaxNewReferrals.js" defer></script>

<?php
include_once 'footer.php'
?>