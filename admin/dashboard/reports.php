<?php
include_once 'header.php';
?>
<div class="report-container">
<div class="accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
      <h4>Generate Report</h4>
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <div class="referral-form">
            <form id="report-form">
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <label>Report Type</label>
                    <select class="form-select" aria-label="Default select example">
                        <option selected>Select Report Type</option>
                        <option value="1">Referrals Overview</option>
                    </select>
                </div>
                <div class="col-lg-6 mb-3">
                    <label>Date From</label>
                    <input type="date" name="date-from" id="date-from" class="form-control" >
                </div>
                <div class="col-lg-6 mb-3">
                    <label>Date To</label>
                    <input type="date" name="date-to" id="date-to" class="form-control" >
                </div>
                <div class="col-lg-12 mb-3">
                    <label>Status</label>
                    <select class="form-select" name="status" id="selected-status">
                        <option selected value="">Select Status</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Declined">Declined</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="report-buttons text-end">
                <button type="submit" name="submit" class="btn btn-primary">Generate</button>
            </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<div class="result-content">

</div>


<script src="js/ajaxReport.js"></script>

<?php
include_once 'footer.php'
?>