<?php
include_once 'header.php';
include_once 'includes/prenatal_functions.inc.php';
$fclt_id = $_SESSION['fcltid'];
$staff_id = $_SESSION["usersid"];
$getAppointment = getAppointment();

$sql1 = "SELECT COUNT(*) as row_count FROM patients WHERE fclt_id = '$fclt_id'";
$sql2 = "SELECT COUNT(*) as row_count FROM patients WHERE staff_id = '$staff_id'";

$result1 = mysqli_query($conn, $sql1);
$result2 = mysqli_query($conn, $sql2);

if ($result1 && $result2) {
    // Step 3: Fetch the results and display the counts
    $patients = mysqli_fetch_assoc($result1);
    $mypatients = mysqli_fetch_assoc($result2);

    $patients = $patients['row_count'];
    $mypatients = $mypatients['row_count'];
}
?>

<div class="main-feed">
  <div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-8 mb-4">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-6 col-lg-12 mb-4">
          <div class="head">
                <h3 class="left-heading mb-4">Patients</h3>
            </div>
          <div class="home-feed">
          <div class="patient-section">
            <div class="patients-card">
              <div class="patient-logo">
                <img src="../../assets/patients.png" alt="Logo" class="logo">
              </div>
              <div class="patient-content">
                <div class="patient-count">
                  <p class="total"><?php echo $patients;?></p>
                  <p class="caption">Facility Total Patients</p>
                </div>
              </div>
            </div>
            <div class="patients-card">
              <div class="patient-logo">
                <img src="../../assets/patients.png" alt="Logo" class="logo">
              </div>
              <div class="patient-content">
                <div class="patient-count">
                  <p class="total"><?php echo $mypatients;?></p>
                  <p class="caption">Your total Patients</p>
                </div>
              </div>
            </div>
          </div>
          </div>
          </div>
          <div class="col-12 col-sm-12 col-md-6 col-lg-12 mb-4">
            <div class="head">
                  <h3 class="left-heading mb-4">Upcoming Appointments</h3>
            </div>
            <div class="home-feed">
            <div class="appoinment_table">
              <table class="table table-borderless" id="appoinment_table">
                <thead>
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Name</th>
                    <th scope="col">Trimester</th>
                    <th scope="col">Checkup</th>
                    <th scope="col">Date of Return</th>
                    <th scope="col">Record</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Loop through the paginated patients and display each in a table row
                  foreach ($getAppointment as $patient) {
                    $id = $patient['schedule_id'];
                    $patient_name = $patient['fname'] . ', ' . $patient['mname'] . $patient['lname'];
                    $trimester = $patient['trimester'];
                    $check_up = $patient['check_up'];
                    $address = $patient['barangay'];
                    $date = $patient['date'];
                    $record = $patient['record'];

                    if (!empty($date)) {
                      $dateTimestamp = strtotime($date);
                      $formatteddate = date('M jS, Y', $dateTimestamp);
                    } else {
                        // Handle empty date case
                        $formatteddate = "No Records";
                    }
                    ?>


                    <tr style="height:10px">
                      <td class="avatar"><img src="../../assets/2ad392c4_🤓.png" alt="Logo" class="logo"></td>
                      <td><?php echo $patient_name?></td>
                      <td><?php echo $trimester?></td>
                      <td><?php echo $check_up?></td>
                      <td><?php echo $formatteddate?></td>
                      <td><?php echo $record?></td>
                      <td><button class="viewAppointment" value="<?php echo $id ?>">Details <i class="fi fi-rr-angle-small-right"></i></button></td>
                    </tr>
                    <?php
                  }
                  ?>
                </tbody>
              </table>
              </div>
            </div>
          </div>
        </div>
    </div>
    
  <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-4 custom-calendar">
      <div class="head">
            <h3 class="left-heading mb-4">Calendar</h3>
      </div>    
      <div class="wrapper">
        <header class="bg-white">
          <h4 class="current-date"></h4>
          <div class="icons">
            <button id="prev" class="material-symbols-rounded">chevron_left</button>
            <button id="next" class="material-symbols-rounded">chevron_right</button>
          </div>
        </header>
        <div class="calendar">
          <ul class="weeks list-unstyled">
            <li class="flex-fill">Sun</li>
            <li class="flex-fill">Mon</li>
            <li class="flex-fill">Tue</li>
            <li class="flex-fill">Wed</li>
            <li class="flex-fill">Thu</li>
            <li class="flex-fill">Fri</li>
            <li class="flex-fill">Sat</li>
          </ul>
          <ul class="days list-unstyled"></ul>
        </div>
      </div>
    </div>
  </div>

  </div>
</div>

<!-- VIEW ARRIVAL REFERRALS -->
<div class="modal fade" id="appointmentModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">Patient Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
          <div class="modal-body">
            <form id="appointment_form">
              <div class="row">
                <div class="col-lg-12 mb-2">
                <label>Patient's Name</label>
                <input type="text" name="patients_name" id="patients_name" class="form-control">
                <input type="hidden" name="appointment_id" id="appointment_id" class="form-control">
                <input type="hidden" name="patients_id" id="patients_id" class="form-control">
                </div>
                <div class="col-lg-12 mb-2">
                <label>Trimester</label>
                <input type="text" name="trimesters" id="trimesters" class="form-control">
                </div>
                <div class="col-lg-12 mb-2">
                <label>Check Up</label>
                <input type="text" name="checkup" id="checkup" class="form-control">
                </div>
                <div class="col-lg-12 mb-2">
                <label>Date of Return</label>
                <input type="date" name="date_of_return" id="date_of_return" class="form-control">
                </div>
                <div class="col-lg-12 mb-2">
                <label>Record</label>
                <input type="text" name="record" id="record" class="form-control">
                </div>
              </div>
          </div>
      <div class="modal-footer">
        <button role="button" class="btn close" id="declineAppointment" style="margin-right: auto;">Declined</button>
        <div class="referral-buttons">
          <button type="button" data-bs-dismiss="modal" class="btn close cancel d-none">Cancel</button>
          <button type="submit" class="btn btn-primary d-none submitBtn">Submit</button>
          <button type="button" class="btn close editBtn">Edit</button>
          <a role="button" class="btn btn-primary proceedBtn">Proceed</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js/calendar_script.js" defer></script>
<script src="js/ajaxPrenatal.js"></script>

<?php
include_once 'footer.php';
?>