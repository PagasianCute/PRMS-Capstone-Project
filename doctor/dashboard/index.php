<?php
include_once 'header.php';
include_once 'includes/prenatal_functions.inc.php';
$getReferrals = getReferrals();
?>


<div class="main-feed">
  <div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-8 mb-4">
      <div class="row">
          <div class="col-12 col-sm-12 col-md-6 col-lg-12 mb-4">
            <div class="head">
                  <h3 class="left-heading mb-4">New Referrals</h3>
            </div>
            <div class="home-feed">
            <div class="appoinment_table">
              <table class="table table-borderless" id="appoinment_table">
                <thead>
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Staff Name</th>
                    <th scope="col">Paient Name</th>
                    <th scope="col">Referring Unit</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $count = 0;
                  // Loop through the paginated patients and display each in a table row
                  foreach ($getReferrals as $patient) {
                    $count++;
                    $name = $patient['name'];
                    $patient_name = $patient['fname'] . ', ' . $patient['mname'] . $patient['lname'];
                    $fclt_name = $patient['fclt_name'];
                    $sent_date = $patient['sent_date'];
                    $sent_time = date("h:i A", strtotime($patient['sent_time']));
                    $img = $patient['img'];
                    ?>


                    <tr style="height:10px">
                      <td class="avatar"><img src="../../assets/<?php echo $img?>" alt="Logo" class="logo"></td>
                      <td><?php echo $patient_name?></td>
                      <td><?php echo $name?></td>
                      <td><?php echo $fclt_name?></td>
                      <td><?php echo $sent_date?></td>
                      <td><?php echo $sent_time?></td>
                      <td><a href="new_referrals.php" class="details-btn">View<i class="fi fi-rr-angle-small-right"></i></a></td>
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
<script src="js/calendar_script.js" defer></script>
<?php
include_once 'footer.php';
?>