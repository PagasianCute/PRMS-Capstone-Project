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
    <table class="table equal-width-table">
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
            <td class="action-column">
            <button id="icon-btn" type="button" value="'.$rffrl_id.'" class="viewRecord"><i class="fi fi-rr-eye"></i></button>
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

<script src="js/ajaxAcceptedReferrals.js"></script>
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