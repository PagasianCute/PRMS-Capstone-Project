<?php
include_once 'header.php';

include_once 'includes/prenatal_functions.inc.php';

$staff_name = $_SESSION["usersname"];

if (isset($_GET['id'])) {
  $patientID = $_GET['id'];

$row = getPatientDetails($conn, $patientID);
$columnNames = ($row) ? array_keys($row) : [];

  // Retrieve patient information from the database using the provided ID
  $sql = "SELECT * FROM patients LEFT JOIN patients_details ON patients.id = patients_details.patients_id WHERE patients.id = ?";
  $stmt = mysqli_stmt_init($conn);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
      // Handle the error appropriately (e.g., show an error message)
      die("Statement preparation failed: " . mysqli_error($conn));
  }

  mysqli_stmt_bind_param($stmt, "i", $patientID);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  // Check if a patient is found with the provided ID
  if ($row = mysqli_fetch_assoc($result)) {
      // You can output other patient information as needed
  } else {
      // Handle the case when no patient is found with the provided ID
      echo "Patient not found.";
  }

  mysqli_stmt_close($stmt);
} else {
  // Handle the case when no ID is provided in the URL
  echo "Invalid patient ID.";
}

?>

    <div class="head">
        <?php
        if ($row) {
            echo '<h2> Patient Name: ' . $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] . '</h2> ';
        }
        ?>
        <div class="head_buttons">
        <div class="col">
        <select class="form-select" name="recordsCount" id="recordsCount" style="min-width: 170px;">
            <!-- PATIENT RECORDS DISPLAY HERE FROM JS  -->
        </select>
        </div>
        <a class="btn btn-primary" id="print-btn" target='_blank' role="button"><i class="fi fi-sr-print"></i> Print</a>
        <button type="button" class="btn btn-primary" id="createNewRecords">Create New Record</button>
        <button type="button" class="btn btn-primary" id="deleteRecord">Delete Record</button>
        <a class="btn btn-primary" href="prenatal.php" role="button">Back</a>
        </div>
    </div>

    <div class="card new_modal" style="margin-bottom: -10px;">
        <div class="card-header" id="hahaha">
        <div class="d-flex justify-content-between" >
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="patient_info-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Patient Information</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="birth_exp-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Birth Experience</a>
                </li>
                <li class="nav-item" role="presentation">
                <a class="nav-link" id="trimesters-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Trimester Records</a>
              </li>
            </ul>
        </div>
      </div>
      <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="patient_info-tab">
          
        <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="asd">
        <h5 class="card-title">Special title treatment</h5>
        <div class="alert alert-warning d-none" id="errorMessage"></div>
        <div class="alert alert-success d-none" id="successMessage"></div>
        <form id="patientsDetailsInsert">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>Date of first Checkup</label>
                    <input type="date" name="petsa_ng_unang_checkup" id="petsa_ng_unang_checkup" class="form-control">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>Weight</label>
                    <input type="text" class="form-control" name="timbang" id="timbang">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>Height</label>
                    <input type="text" class="form-control" name="taas" id="taas">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>Health Status</label>
                    <input type="text" class="form-control" name="kalagayan_ng_kalusugan" id="kalagayan_ng_kalusugan">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>Date of last Period</label>
                    <input type="date" name="petsa_ng_huling_regla" id="petsa_ng_huling_regla" class="form-control">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>When to give Birth</label>
                    <input type="date" name="kailan_ako_manganganak" id="kailan_ako_manganganak" class="form-control">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label>Birth Count</label>
                    <input type="number" class="form-control" name="pang_ilang_pagbubuntis" id="pang_ilang_pagbubuntis">
                </div>
                <div class="col-12" style="margin-top: 15px;">
                    <button type="submit" id="patientSave" class="btn btn-primary">Save</button>
                    <button type="submit" id="patientUpdate" class="btn btn-primary d-none">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="birth_exp-tab">
    <h5 style="margin-bottom: 15px;">Karanasan sa mga Naunang Pagbubuntis at Panganganak</h5>
    <form id="birthExperienceInsert" class="row">
        <div class="col-sm-4 mb-3">
            <label>Date of delivery</label>
            <input type="date" name="date_of_delivery" id="date_of_delivery" class="form-control">
        </div>
        <div class="col-sm-4 mb-3">
            <label>Type of delivery</label>
            <select class="form-select" name="type_of_delivery" id="type_of_delivery">
                <option selected value="">Choose...</option>
                <option value="Normal (N)">Normal (N)</option>
                <option value="Ceasarean Delivery (C/S)">Ceasarean Delivery (C/S)</option>
            </select>
        </div>
        <div class="col-sm-4 mb-3">
            <label>Birth Outcome</label>
            <select class="form-select" name="birth_outcome" id="birth_outcome">
                <option selected value="">Choose...</option>
                <option value="Alive">Alive</option>
                <option value="Miscarriage">Miscarriage</option>
                <option value="Stillbirth">Stillbirth</option>
            </select>
        </div>
        <div class="col-sm-4 mb-3">
            <label>Number of Child / Children delivered</label>
            <select class="form-select" name="number_of_children_delivered" id="number_of_children_delivered">
                <option selected value="">Choose...</option>
                <option value="Single">Single</option>
                <option value="Twins">Twins</option>
                <option value="Multiple Birth">Multiple Birth</option>
            </select>
        </div>
        <div class="col-12" style="margin-bottom: 15px;  margin-top: 10px">
            <h5>Pregnancy-related Conditions / Complications</h5>
        </div>
        <div class="col-sm-4 mb-3">
            <label>Pregnancy Included Hypertension (PIH) (Y/N)</label>
            <select class="form-select" name="pregnancy_hypertension" id="pregnancy_hypertension">
                <option selected value="">Yes or NO</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="col-sm-4 mb-3">
            <label>Preeclampsia / Eclampsia (PE/E) (Y/N)</label>
            <select class="form-select" name="preeclampsia_eclampsia" id="preeclampsia_eclampsia">
                <option selected value="">Yes or NO</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="col-sm-4 mb-3">
            <label>Bleeding during pregnancy or after delivery (Y/N)</label>
            <select class="form-select" name="bleeding_during_pregnancy" id="bleeding_during_pregnancy">
                <option selected value="">Yes or NO</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="col-12" style="margin-top: 15px;">
            <button type="submit" id="birthExSave" class="btn btn-primary">Save</button>
            <button type="submit" id="birthExUpdate" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>


  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="trimesters-tab">
        
    <div class="card new_modal" id="trimester">
        <div class="card-header" id="hehehe">
            <div class="d-flex justify-content-between" id="trimester-header">
                <ul class="nav nav-tabs card-header-tabs button-container-left" style="margin-left:1px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link left-button active" id="first_trimester-tab" data-bs-toggle="tab" data-tab="first_trimester" data-bs-target="#first_trimester" type="button" role="tab" aria-controls="first_trimester" aria-selected="true">First Trimester</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link left-button" id="second_trimester-tab" data-bs-toggle="tab" data-tab="second_trimester" data-bs-target="#second_trimester" type="button" role="tab" aria-controls="second_trimester" aria-selected="false">Second Trimester</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link left-button" id="third_trimester-tab" data-bs-toggle="tab" data-tab="third_trimester" data-bs-target="#third_trimester" type="button" role="tab" aria-controls="third_trimester" aria-selected="false">Third Trimester</button>
                    </li>
                </ul>
                <ul class="nav nav-tabs card-header-tabs second-nav-tabs button-container-right">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link right-button active" id="first_check_up-tab" data-bs-toggle="tab" data-tab="first_checkup"  type="button" role="tab" aria-controls="home" aria-selected="true">First Check-up</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link right-button" id="second_check_up-tab" data-bs-toggle="tab" data-tab="second_checkup"  type="button" role="tab" aria-controls="profile" aria-selected="false">Second Check-up</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link right-button" id="third_check_up-tab" data-bs-toggle="tab" data-tab="third_checkup" type="button" role="tab" aria-controls="contact" aria-selected="false">Third Check-up</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="first_trimester" role="tabpanel" aria-labelledby="first_trimester-tab">
                    <form id="firstTrimesterInsert" class="row trimester-form">
                        <div class="col-sm-4 mb-3">
                            <label>Date:</label>
                            <input type="date" name="firstTri_date" id="firstTri_date" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Weight:</label>
                            <input type="text" name="firstTri_weight" id="firstTri_weight" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Height:</label>
                            <input type="text" name="firstTri_height" id="firstTri_height" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Age of Gestation:</label>
                            <input type="text" name="firstTri_age_of_gestation" id="firstTri_age_of_gestation" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Blood Pressure:</label>
                            <input type="text" name="firstTri_blood_pressure" id="firstTri_blood_pressure" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Nutritional Status:</label>
                            <select class="form-select" name="firstTri_nutritional_status" id="firstTri_nutritional_status">
                                <option selected value="">Choose...</option>
                                <option value="Normal">Normal</option>
                                <option value="Underweight">Underweight</option>
                                <option value="Overweight">Overweight</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Laboratory Tests Done:</label>
                            <input type="text" name="firstTri_laboratory_tests_done" id="firstTri_laboratory_tests_done" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Hemoglobin Count:</label>
                            <input type="text" name="firstTri_hemoglobin_count" id="firstTri_hemoglobin_count" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Urinalysis:</label>
                            <input type="text" name="firstTri_urinalysis" id="firstTri_urinalysis" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Complete Blood Count (CBC):</label>
                            <input type="text" name="firstTri_complete_blood_count" id="firstTri_complete_blood_count" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>STIs using a syndromic approach:</label>
                            <select class="form-select" name="firstTri_stis_using_a_syndromic_approach" id="firstTri_stis_using_a_syndromic_approach">
                                <option selected value="">Choose...</option>
                                <option value="Syphilis">Syphilis</option>
                                <option value="HIV">HIV</option>
                                <option value="Hipatitis B (HbsAg)">Hipatitis B (HbsAg)</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Tetanus-containing Vaccine:</label>
                            <input type="text" name="firstTri_tetanus_containing_vaccine" id="firstTri_tetanus_containing_vaccine" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="hospital_referral">Given Services:</label>
                            <select class="form-select" name="firstTri_given_services" id="firstTri_given_services">
                                <option selected value="">Choose...</option>
                                <option value="Avoiding alcohol, tobacco, and illegal drugs">Avoiding alcohol, tobacco, and illegal drugs</option>
                                <option value="Counseling about proper diet">Counseling about proper diet</option>
                                <option value="Counseling about safe sex">Counseling about safe sex</option>
                                <option value="Use of insecticide-treated mosquito nets">Use of insecticide-treated mosquito nets</option>
                                <option value="Birth Plan">Birth Plan</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Date of Return:</label>
                            <input type="date" name="firstTri_date_of_return" id="firstTri_date_of_return" class="form-control" required>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Health Provider Name:</label>
                            <input type="text" name="firstTri_health_provider_name" id="firstTri_health_provider_name" class="form-control" value="<?php echo $staff_name?>" readonly>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Select Refer Hospital</label>
                            <div class="col-lg-12 col-md-6 col-sm-12">
                            <select class="form-select" name="firstTri_hospital_referral" id="firstTri_hospital_referral">
                            <option selected value=""></option>
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
                        <div class="col-12">
                            <button type="submit" id="firstTriSave" class="btn btn-primary">Save</button>
                            <button type="submit" id="firstTriUpdate" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="second_trimester" role="tabpanel" aria-labelledby="second_trimester-tab">
                <form id="secondTrimesterInsert" class="row trimester-form">
                        <div class="col-sm-4 mb-3">
                            <label>Date:</label>
                            <input type="date" name="secondTri_date" id="secondTri_date" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Weight:</label>
                            <input type="text" name="secondTri_weight" id="secondTri_weight" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Height:</label>
                            <input type="text" name="secondTri_height" id="secondTri_height" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Age of Gestation:</label>
                            <input type="text" name="secondTri_age_of_gestation" id="secondTri_age_of_gestation" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Blood Pressure:</label>
                            <input type="text" name="secondTri_blood_pressure" id="secondTri_blood_pressure" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Nutritional Status:</label>
                            <select class="form-select" name="secondTri_nutritional_status" id="secondTri_nutritional_status">
                                <option selected value="">Choose...</option>
                                <option value="Normal">Normal</option>
                                <option value="Underweight">Underweight</option>
                                <option value="Overweight">Overweight</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Given Advise</label>
                            <input type="text" name="secondTri_given_advise" id="secondTri_given_advise" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Laboratory Tests Done:</label>
                            <input type="text" name="secondTri_laboratory_tests_done" id="secondTri_laboratory_tests_done" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Urinalysis:</label>
                            <input type="text" name="secondTri_urinalysis" id="secondTri_urinalysis" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Complete Blood Count (CBC):</label>
                            <input type="text" name="secondTri_complete_blood_count" id="secondTri_complete_blood_count" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Given Services:</label>
                            <select class="form-select" name="secondTri_given_services" id="secondTri_given_services">
                                <option selected value="">Choose...</option>
                                <option value="Avoiding alcohol, tobacco, and illegal drugs">Avoiding alcohol, tobacco, and illegal drugs</option>
                                <option value="Counseling about proper diet">Counseling about proper diet</option>
                                <option value="Counseling about safe sex">Counseling about safe sex</option>
                                <option value="Use of insecticide-treated mosquito nets">Use of insecticide-treated mosquito nets</option>
                                <option value="Birth Plan">Birth Plan</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Date of Return:</label>
                            <input type="date" name="secondTri_date_of_return" id="secondTri_date_of_return" class="form-control" required>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Health Provider Name:</label>
                            <input type="text" name="secondTri_health_provider_name" id="secondTri_health_provider_name" class="form-control" value="<?php echo $staff_name?>" readonly>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Select Refer Hospital</label>
                            <div class="col-lg-12 col-md-6 col-sm-12">
                            <select class="form-select" name="secondTri_hospital_referral" id="secondTri_hospital_referral">
                            <option selected value=""></option>
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
                        <div class="col-12">
                            <button type="submit" id="secondTriSave" class="btn btn-primary">Save</button>
                            <button type="submit" id="secondTriUpdate" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="third_trimester" role="tabpanel" aria-labelledby="third_trimester-tab">
                <form id="thirdTrimesterInsert" class="row trimester-form">
                        <div class="col-sm-4 mb-3">
                            <label>Date:</label>
                            <input type="date" name="thirdTri_date" id="thirdTri_date" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Weight:</label>
                            <input type="text" name="thirdTri_weight" id="thirdTri_weight" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Height:</label>
                            <input type="text" name="thirdTri_height" id="thirdTri_height" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Age of Gestation:</label>
                            <input type="text" name="thirdTri_age_of_gestation" id="thirdTri_age_of_gestation" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Blood Pressure:</label>
                            <input type="text" name="thirdTri_blood_pressure" id="thirdTri_blood_pressure" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Nutritional Status:</label>
                            <select class="form-select" name="thirdTri_nutritional_status" id="thirdTri_nutritional_status">
                                <option selected value="">Choose...</option>
                                <option value="Normal">Normal</option>
                                <option value="Underweight">Underweight</option>
                                <option value="Overweight">Overweight</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Given Advise</label>
                            <input type="text" name="thirdTri_given_advise" id="thirdTri_given_advise" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Laboratory Tests Done:</label>
                            <input type="text" name="thirdTri_laboratory_tests_done" id="thirdTri_laboratory_tests_done" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Urinalysis:</label>
                            <input type="text" name="thirdTri_urinalysis" id="thirdTri_urinalysis" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Complete Blood Count (CBC):</label>
                            <input type="text" name="thirdTri_complete_blood_count" id="thirdTri_complete_blood_count" class="form-control">
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="hospital_referral">Given Services:</label>
                            <select class="form-select" name="thirdTri_given_services" id="thirdTri_given_services">
                                <option selected value="">Choose...</option>
                                <option value="Avoiding alcohol, tobacco, and illegal drugs">Avoiding alcohol, tobacco, and illegal drugs</option>
                                <option value="Counseling about proper diet">Counseling about proper diet</option>
                                <option value="Counseling about safe sex">Counseling about safe sex</option>
                                <option value="Use of insecticide-treated mosquito nets">Use of insecticide-treated mosquito nets</option>
                                <option value="Birth Plan">Birth Plan</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Date of Return:</label>
                            <input type="date" name="thirdTri_date_of_return" id="thirdTri_date_of_return" class="form-control" required>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Health Provider Name:</label>
                            <input type="text" name="thirdTri_health_provider_name" id="thirdTri_health_provider_name" class="form-control" value="<?php echo $staff_name?>" readonly>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label>Select Refer Hospital</label>
                            <div class="col-lg-12 col-md-6 col-sm-12">
                            <select class="form-select" name="thirdTri_hospital_referral" id="thirdTri_hospital_referral">
                            <option selected value=""></option>
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
                        <div class="col-12">
                            <button type="submit" id="thirdTriSave" class="btn btn-primary">Save</button>
                            <button type="submit" id="thirdTriUpdate" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>  
</div>
      </div>

<script src="js/ajaxViewPrenatal.js"></script>
      
<?php
include_once 'footer.php';
?>