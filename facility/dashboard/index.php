<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';
include_once 'includes/messages_functions.inc.php';
include_once 'includes/prenatal_functions.inc.php';
$fclt_id = $_SESSION['fcltid'];
$fclt_type = $_SESSION["fclttype"];
$contacts = contacts();
$messages = messages();
$prenatals = prenatals();
$doctors = doctors();
$giveBirth = getGiveBirth();
$currentMonth = date('n');

// Call the function and fetch all the referrals
$displayreferrals = displayAllReferralsPending();
$getreferral = getAllReferrals();
$getminireferral = minireferrals();
$forReferrralPatients = forReferrralPatients();

if($fclt_type == 'Hospital' || $fclt_type == 'Provincial Hospital'){
  
  $sql1 = "SELECT COUNT(*) as row_count FROM prenatal_records";
  $sql2 = "SELECT COUNT(*) as row_count FROM referral_records WHERE referred_hospital = '$fclt_id'";
  $sql3 = "SELECT COUNT(*) as row_count FROM patients_details INNER JOIN patients ON patients_details.patients_id = patients.id";
  
  $prenatalRecords = getPrenatalRecordsHospital();
}else{
  $sql1 = "SELECT COUNT(*) as row_count FROM prenatal_records WHERE fclt_id = '$fclt_id'";
  $sql2 = "SELECT COUNT(*) as row_count FROM referral_records WHERE fclt_id = '$fclt_id'";
  $sql3 = "SELECT COUNT(*) as row_count FROM patients_details INNER JOIN patients ON patients_details.patients_id = patients.id WHERE patients.fclt_id = '$fclt_id' AND MONTH(patients_details.kailan_ako_manganganak) = MONTH(CURDATE())";
  
  $prenatalRecords = getPrenatalRecordsBirthing();
}

$result1 = mysqli_query($conn, $sql1);
$result2 = mysqli_query($conn, $sql2);
$result3 = mysqli_query($conn, $sql3);

if ($result1 && $result2 && $result3) {
    // Step 3: Fetch the results and display the counts
    $patients = mysqli_fetch_assoc($result1);
    $referral = mysqli_fetch_assoc($result2);
    $birth = mysqli_fetch_assoc($result3);

    $patients = $patients['row_count'];
    $referral = $referral['row_count'];
    $birth = $birth['row_count'];
}
?>
<div class="main-cards">
  <div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
      <div class="mini-cards" id="card1">
        <div class="mini-logo">
          <i class="fi fi-rr-child-head"></i>
        </div>
        <div class="mini-card-content">
          <h2 class="mini-name"><?php echo $patients;?></h2>
          <p class="mini-description">Total Prenatal</p>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="mini-cards" id="card2">
        <div class="mini-logo">
        <i class="fi fi-rs-memo-circle-check"></i>
        </div>
        <div class="mini-card-content">
          <h2 class="mini-name"><?php echo $referral;?></h2>
          <p class="mini-description">Total Referral</p>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="mini-cards" id="card3">
        <div class="mini-logo">
        <i class="fi fi-rr-person-pregnant"></i>
        </div>
        <div class="mini-card-content">
          <h2 class="mini-name"><?php echo $birth;?></h2>
          <p class="mini-description">Total Giving Birth</p>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="mini-cards" id="card4">
        <div class="mini-logo">
        <i class="fi fi-bs-users"></i>
        </div>
        <div class="mini-card-content">
          <h2 class="mini-name"><?php echo $referral;?></h2>
          <p class="mini-description">Total Patient</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="main-feed">
  <div class="row">
  <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-4">
        <div class="head">
              <h3 class="left-heading mb-4">Referrals</h3>
            </div>
          <div class="home-feed">
          <div class="yourDivClass" id="referralDiv">
  <?php
      $count=0;
              // Loop through the referrals and display each patient in a table row
              foreach ($getminireferral as  $referrals) {
                  $fclt_name = $referrals['fclt_name'];
                  $time = $referrals['time'];
                  $name = $referrals['name'];
                  $status = $referrals['status'];
                  $convertedTime = date('h:i A', strtotime($time));

                  if($_SESSION["fclttype"] == 'Birthing Home'){
                  ?>
          <div class="referral-card">
              <div class="mini-referral-logo" id="referral-logo">
                <img src="assets/medical-report.png" alt="Logo" class="logo">
              </div>
                <div class="info">
                    <div class="name"><?= $fclt_name ?></div>
                  <?php
                    if($time == NULL){
                      ?>
                      <div class="description"><?= $name ?> • <?= $convertedTime?></div>
                      <?php
                    }else{
                      ?>
                  <div class="description"><?= $name ?> • <?= $convertedTime?></div>
                      <?php
                    }
                  ?>
                </div>
                <?php 
                if($status == 'Sent To a Doctor'){
                  $status_id = "on_process";
                  $status = "On Process";
                }else {
                  $status_id = $status;
                  $status = $status;
                }
                ?>
                <div class="mini-status" id="<?= $status_id ?>btn"><?= $status ?></div>
            </div>
            <?php
            }else{
              ?>

              <div class="referral-card">
              <div class="mini-referral-logo" id="referral-logo">
                <img src="assets/medical-report.png" alt="Logo" class="logo">
              </div>
                <div class="info">
                    <div class="name"><?= $fclt_name ?></div>
                  <?php
                    if($time == NULL){
                      ?>
                      <div class="description"><?= $name ?> • <?= $convertedTime?></div>
                      <?php
                    }else{
                      ?>
                  <div class="description"><?= $name ?> • <?= $convertedTime?></div>
                      <?php
                    }
                  ?>
                </div>
                <?php 
                if($status == 'Sent To a Doctor'){
                  $status_id = "on_process";
                  $status = "On Process";
                }else {
                  $status_id = $status;
                  $status = $status;
                }
                ?>
                <div class="mini-status" id="<?= $status_id ?>btn"><?= $status ?></div>
            </div>

          <?php

            }
      $count++;
        }
        if($count==0){
          echo"No New Referrals";
        }
      ?>
        </div>
        </div>
        </div>

    <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-4">
    <div class="head">
          <h3 class="left-heading mb-4">Prenatal</h3>
        </div>
      <div class="home-feed">
        <div class="yourDivClass">
        
        <?php
         $count=0;
        if($fclt_type == 'Hospital' || $fclt_type == 'Provincial Hospital'){
         
          foreach ($prenatalRecords as $record) {
            $count++;
            $fclt_name = $record['fclt_name'];
            $total = $record['total_rows'];
        ?>
            <div class="referral-card">
                <div class="mini-referral-logo" id="prenatal-logo">
                    <i class="fi fi-rr-child-head"></i>
                </div>
                <div class="info">
                    <div class="name"><?= $fclt_name?></div>
                    <div class="description"><?= $total . ' • Total Prenatal' ?></div>
                </div>
                <a href="prenatal_list.php" class="confirm-button" id="viewbtn">View</a>
            </div>
        <?php
        }
        if($count==0){
          echo"No New Records";
        }
        }else{

          foreach ($prenatalRecords as $record) {
            $count++;
            $fname = $record['fname'];
            $mname = $record['mname'];
            $lname = $record['lname'];
            $date = $record['date'];
            $time = $record['time'];
            $convertedTime = date('h:i A', strtotime($time));
            $patients_id = $record['patients_id'];
        ?>
            <div class="referral-card">
                <div class="mini-referral-logo" id="prenatal-logo">
                    <i class="fi fi-rr-child-head"></i>
                </div>
                <div class="info">
                    <div class="name"><?= $lname . ', ' . $fname . ' ' . $mname ?></div>
                    <div class="description"><?= $date . ' • '. $convertedTime ?></div>
                </div>
                <a role="button" href="`view_patient.php?id=<?= $patients_id ?>" class="confirm-button" id="viewbtn">View</a>
            </div>
        <?php
        }
        if($count==0){
          echo"No New Records";
        }
        }
        ?>


        </div>
      </div>
    </div>
 <!-- Additional form fields for name and description -->
    <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-4">
    <div class="head">
          <h3 class="left-heading mb-4">Messages</h3>
        </div>
      <div class="home-feed">
        <div class="yourDivClass">
          <div class="contacts">

          </div>
        </div>
      </div>
    </div>

    <?php if($fclt_type == 'Birthing Home'){ ?>
    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
        <div class="head">
              <h3 class="left-heading mb-4">For Referral Patients</h3>
            </div>
          <div class="home-feed">
          <div class="yourDivClass">
  <?php
      $count=0;
              // Loop through the referrals and display each patient in a table row
              foreach ($forReferrralPatients as  $data) {
                  $patient_fname = $data['patient_fname'] . ', '. $data['patient_mname'] . ' ' . $data['patient_lname'];
                  $staff_fname = $data['staff_fname'] . ', '. $data['staff_mname'] . ' ' . $data['staff_lname'];
                  $facility_name = $data['facility_name'];
                  $img = $data['img'];
                  $from_facility = $data['from_facility'];

                  if($_SESSION["fclttype"] == 'Birthing Home'){
                  ?>
          <div class="referral-card">
              <div class="mini-referral-logo" id="referral-logo">
                <img src="assets/referral.png" alt="Logo" class="logo">
              </div>
                <div class="info">
                    <div class="name mb-1"><?= $facility_name ?></div>
                  <div class="description">Health Provider: <?= $staff_fname ?></div>
                      <?php
                  ?>
                </div>
                <div class="staff-icon">
                    <img src="../../assets/<?= $img ?>" alt="">
                </div>
            </div>
            <?php
            }else{
              ?>

              <div class="referral-card">
              <div class="mini-referral-logo" id="referral-logo">
                <img src="assets/referral.png" alt="Logo" class="logo">
              </div>
                <div class="info">
                    <div class="name"><?= $from_facility ?></div>
                  <div class="description">Health Provider Name: <?= $staff_fname ?></div>
 
                </div>
                <div class="staff-icon">
                    <img src="../../assets/<?= $img ?>" alt="">
                </div>
            </div>

          <?php

            }
      $count++;
        }
        if($count==0){
          echo"No New Referrals";
        }
      ?>
        </div>
        </div>
        </div>
        <?php }?>

    <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-4">
    <div class="head">
          <h3 class="left-heading mb-4">Giving Birth this Month</h3>
        </div>
      <div class="home-feed">
        <div class="yourDivClass">
        <?php
        if (!empty($giveBirth)) { // Check if $giveBirth is not empty
            foreach ($giveBirth as $records) {
                $fclt_name = $records['fclt_name'];
                $total = $records['count'];
                $img_url = $records['img_url'];
                ?>
                <div class="referral-card">
                    <div class="messages-mini-referral-logo" id="message-logo">
                        <img src="../../assets/<?= $img_url ?>" alt="Logo">
                    </div>
                    <div class="info">
                        <div class="name"><?= $fclt_name ?></div>
                        <div class="description"><?= $total . ' • Total of Giving Birth this month' ?></div>
                    </div>
                    <a href="calendar.php" class="confirm-button" id="viewbtn">View</a>
                </div>
                <?php
            }
        } else { // If no records exist
            echo "No New Referrals";
        }
        ?>

        </div>
      </div>
    </div>

  </div>
</div>

    <!-- STAFF LOGIN FORM -->
  <div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
      <div class="body-title">
      <h1>Log in</h1>
      <h5>staff log in</h5>
      </div>
        <form id="loginStaff">
        <div class="mb-3">
            <label for="uid" id = "asd">Username</label>
            <input type="text" class="form-control" name="uid" id="uid" placeholder="Enter username">
        </div>
        <div class="mb-3">
            <label for="uid">Password</label>
            <input type="password" class="form-control" name="pwd" id="pwd" placeholder="Enter password">
        </div>
        <div class="mb-3 button-container">
        <div class="alert alert-danger d-none" id="errorMessage"></div>
        <button type="submit" class="btn btn-primary">Log in</button>
        <a class="btn btn-primary" href="../../index.php" role="button" style="margin-right:auto">Go back to Home Page</a>
        <p class="button-text">Dont have an account? <a href="signup.php">Contact Admin.</a></p>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    var fclt_id = "<?php echo $fclt_id ?>";
</script>

<script src="js/ajaxIndexContacts.js"></script>
<?php
include_once 'footer.php'
?>