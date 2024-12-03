<?php
include_once 'header.php';
include_once 'includes/referral_functions.inc.php';

$itemsPerPage = 9;

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$displayreferrals = myReferrals($page, $itemsPerPage);
$currentYear = date("Y");
$sql_old = "SELECT COUNT(*) as row_count FROM referral_records WHERE (YEAR(date) != '$currentYear' AND fclt_id = '$fclt_id') OR YEAR(date) != '$currentYear' AND (referred_hospital = '$fclt_id')";
$sql_new = "SELECT COUNT(*) as row_count FROM referral_records WHERE (YEAR(date) = '$currentYear' AND fclt_id = '$fclt_id') OR  YEAR(date) = '$currentYear' AND (referred_hospital = '$fclt_id')";
$result_old = mysqli_query($conn, $sql_old);
$result_new = mysqli_query($conn, $sql_new);

if ($result_old && $result_new) {
  $referral_old = mysqli_fetch_assoc($result_old);
  $referral_new = mysqli_fetch_assoc($result_new);

  $referral_old = $referral_old['row_count'];
  $referral_new = $referral_new['row_count'];
}
?>

<script src="js/ajaxFunctions.js"></script>
<script src="js/filter.js"></script>
<script src="js/main.js"></script>

<div class="main-cards">
  <div class="mini-cards">
    <div class="mini-cards-header">
      <div class="mini-logo" id="old">
      <i class="fi fi-rr-form"></i>
      </div>
      <h6>Old Referrals</h6>
    </div>
    <div class="mini-card-content">
      <h2 class="mini-name"><?php echo $referral_old ?></h2>
      <p class="mini-description">Past Years</p>
    </div>
  </div>
  <div class="mini-cards">
    <div class="mini-cards-header">
      <div class="mini-logo" id="new">
      <i class="fi fi-rr-form"></i>
      </div>
      <h6>New Referrals</h6>
    </div>
    <div class="mini-card-content">
      <h2 class="mini-name"><?php echo $referral_new ?></h2>
      <p class="mini-description">This Year</p>
    </div>
  </div>
</div>

<div class="feed">
<div class="head">
    <h2>Referrals List</h2> 
  </div>
<div id="yourDivId" class="yourDivClass">
 <div class="table-header">
 <div class="col-2">
  <input type="text" id="patients_name" class="form-control" placeholder="Search Name">
  </div>
  <div class="col-2">
  <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fi fi-rr-settings-sliders"></i> Filter</button>
  </div>
 </div>
<div class="table-responsive">
    <table id="referralsTable" class="table">
      <thead class="table-light">
        <tr>
          <th scope="col">Referral ID</th>
          <th scope="col">From</th>
          <th scope="col">To</th>
          <th scope="col" class="action-column">Status</th>
          <th scope="col">Date • Time</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $count = 0;
        // Loop through the referrals and display each patient in a table row
        foreach ($displayreferrals as $key => $displayreferrals) {
          $count++;
          $rffrl_id = $displayreferrals['rfrrl_id'];
          $fclt_name = $displayreferrals['fclt_name'];
          $rfrrd_hospital = $displayreferrals['referred_hospital_name'];
          $Name = $displayreferrals['name'];
          $status = $displayreferrals['status'];
          $date = $displayreferrals['date'];
          $time = $displayreferrals['time'];

          ?>
          <tr>
          <th><?php echo $rffrl_id ?></th>
          <td><?php echo $fclt_name ?></td>
          <td><?php echo $rfrrd_hospital ?></td>
          
          <td class="action-column" id="<?php echo $status ?>-column"><p><?php echo $status ?></p></td>
          <td><?php echo $date ?> • <?php echo $time ?></td>
        </tr>
        <?php

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
  $totalPages = ceil(getTotalReferrals() / $itemsPerPage);

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
    
    <!-- VIEW REFERRAL  -->
    <div class="modal fade" id="referralModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content new_modal">
      <div class="modal-header">
        <h5 class="modal-title">To: <span id="fclt_name"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="upperBtn">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Referral Record</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Other Records</a>
          </li>
        </ul>
      </div>
      <div class="modal-body">
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <form id="referral_form">
              <div class="row">
                <input type="hidden" name="rffrl_id" id="rffrl_id" class="form-control">
                <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
                <label>Name</label>
                <input type="text" id="view_name" class="form-control">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
                <label>Age</label>
                <input type="text" id="view_age" class="form-control">
                </div>
              </div>
              </div>
              <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                Profile content
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
        <button type="button" data-bs-dismiss="modal" class="btn close">Close</button>
    </div>
    </form>
        </div>
        </div>
        </div>

   
<!-- CREATE REFERRAL -->
<div class="modal fade" id="createReferralModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="createReferralModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content new_modal">
      <div class="modal-header">
      <h2 class="modal-title" id="createReferralModalLabel">Create Referral</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="upperBtn">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="referral-tab" data-bs-toggle="tab" href="#referral" role="tab" aria-controls="referral" aria-selected="true">Referral Record</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="records-tab" data-bs-toggle="tab" href="#records" role="tab" aria-controls="records" aria-selected="false">Other Records</a>
          </li>
        </ul>
      </div>
      <div class="modal-body">
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="referral" role="tabpanel" aria-labelledby="referral-tab">
            <div class="alert alert-danger d-none" id="referralError"></div>
          <form id="createReferral">
            <div class="row">
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Name</label>
              <input type="text" name="name" id="name" class="form-control" required>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Age</label>
              <input type="text" name="age" id="age" class="form-control">
              </div>
              <!-- 
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Sex</label>
              <input type="text" name="sex" id="sex" class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Birth Date</label>
              <input type="date" name="bdate" id="bdate" class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Sex</label>
              <input type="text" name="sex" id="sex" class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Address</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Admitting Dx</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Rtpcr</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Antigen</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Clinical ssx of covid</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Exposure to covid</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Temp</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>HR</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Resp</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Bp</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>O2sat</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>O2aided</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Procedures needed</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>FH</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>IE</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>FHT</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>LMP</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>EDC</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>AOG</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>UTZ</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>UTZ AOG</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>EDD</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Enterpretation</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
              <label>Diagnostic test</label>
              <input type="text" name="address" id="address " class="form-control">
              </div>
 -->
              <div class="col-sm-12 col-md-6 col-lg-2 mb-2">
                <label>Select Refer Hospital</label>
                <select class="form-select" name="referred_hospital" required>
                <option selected disabled value=""></option>
                <?php 
                $query = "SELECT * FROM facilities WHERE fclt_id != '" . $_SESSION["fcltid"] . "'";
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
          </div>
          <div class="tab-pane fade" id="records" role="tabpanel" aria-labelledby="records-tab">
          <div id="draggableDiv" class="draggable" draggable="true">Drag records here!</div>
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

<?php
include_once 'footer.php'
?>