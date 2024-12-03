<?php
include_once 'header.php';
include_once 'includes/patient_functions.inc.php';

$patient_id = $_GET['id'];

$getAttachments = getAttachments();
$getAppointments = getAppointment();

$sql1 = "SELECT * FROM patients WHERE id = '$patient_id'";
$sql2 = "SELECT COUNT(*) as row_count FROM referral_records WHERE patients_id = '$patient_id'";
$sql3 = "SELECT COUNT(*) as row_count FROM prenatal_records WHERE patients_id = '$patient_id'";

$result1 = mysqli_query($conn, $sql1);
$result2 = mysqli_query($conn, $sql2);
$result3 = mysqli_query($conn, $sql3);

if ($result1 && $result2 && $result3) {
    // Step 3: Fetch the results and display the counts
    $patient = mysqli_fetch_assoc($result1);
    $prenatal = mysqli_fetch_assoc($result3);
    $referral = mysqli_fetch_assoc($result2);

    $prenatal = $prenatal['row_count'];
    $referral = $referral['row_count'];
}

$birthdateTimestamp = strtotime($patient['birthdate']);
$formattedBirthDate = date('M jS, Y', $birthdateTimestamp);

$registeredDateTimestamp = strtotime($patient['date_registered']);
$formattedRegisteredDate = date('M jS, Y', $registeredDateTimestamp);

?>

<div class="parent-container">
    <div class="patient-head-buttons">
        <select class="form-select mb-2" name="recordsCount" id="recordsCount">
            <!-- PATIENT RECORDS DISPLAY HERE FROM JS -->
        </select>
        <a class="btn btn-primary" role="button" href="patients.php">Back</a>
    </div>
</div>

<div class="view-patient">
<div class="row gx-3">
    <div class="col-md-12 col-lg-8 mb-2">
            <div class="row gx-1">
                <div class="col-md-4 col-sm-12 mb-2">
                    <div class="bg-light-subtle info-section shadow-sm">
                        <div class="d-flex justify-content-center patient-image">
                            <img src="../../assets/female-avatar.png" alt="">
                        </div>
                        <div class="patient-info">
                            <h4><?php echo !empty($patient['lname']) ? $patient['lname'] . ', ' : 'none, '; ?><?php echo !empty($patient['fname']) ? $patient['fname'] . ' ' : 'none '; ?><?php echo !empty($patient['mname']) ? $patient['mname'] : ''; ?></h4>
                            <p><?php echo !empty($patient['email']) ? $patient['email'] : 'none'; ?></p>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="row patient-details">
                                <div class="col past border-end">
                                    <h6><?php echo $referral ?></h6>
                                    <span>Referral</span>
                                </div>
                                <div class="col upcoming">
                                    <h6><?php echo $prenatal ?></h6>
                                    <span>Prenatal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12 mb-2">
                    <div class="bg-light-subtle others-section shadow-sm">
                    <div class="row">
                        <div class="col-4 patient-others-col patient-others-col">
                            <div class="patient-others border-bottom">
                                <?php
                                    if (!empty($patient['birthdate'])) {
                                        $birthdate = new DateTime($patient['birthdate']);
                                        $currentDate = new DateTime();
                                        $age = $currentDate->diff($birthdate)->y;
                                    } else {
                                        $age = 'none';
                                    }
                                ?>
                                <p class="label">Age</p>
                                <p id="updated-age"><?php echo $age; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Gender</p>
                                <p><?php echo !empty($patient['gender']) ? $patient['gender'] : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Birthday</p>
                                <p><?php echo !empty($formattedBirthDate) ? $formattedBirthDate : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Phone Number</p>
                                <p><?php echo !empty($patient['contact']) ? $patient['contact'] : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Region</p>
                                <p><?php echo !empty($patient['region']) ? $patient['region'] : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Province</p>
                                <p><?php echo !empty($patient['province']) ? $patient['province'] : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Municipality</p>
                                <p><?php echo !empty($patient['municipality']) ? $patient['municipality'] : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Barangay</p>
                                <p><?php echo !empty($patient['barangay']) ? $patient['barangay'] : 'none'; ?></p>
                            </div>
                        </div>
                        <div class="col-4 patient-others-col">
                            <div class="patient-others border-bottom">
                                <p class="label">Registered Date</p>
                                <p><?php echo !empty($formattedRegisteredDate) ? $formattedRegisteredDate : 'none'; ?></p>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-md-12 col-lg-4 mb-2">
        <div class="bg-light-subtle note-section shadow-sm">
            <div class="note-header">
                <h6>Note</h6>
            </div>
            <form id="patient_note">
            <textarea class="form-control mb-3" name="note" placeholder="Patient's Note" id="note" rows="9"></textarea>
            <button type="submit" class="btn btn-primary note-btn">Save Note</button>
            </form>
        </div>
    </div>
    <div class="col-md-12 col-lg-8 mb-2">
        <div class="row gx-2">
            <div class="col-12">
                <div class="p-3 bg-light-subtle records-section shadow-sm" id="records-section">
                    <div class="nav-tabs-header">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="appointment-tab" data-bs-toggle="pill" href="#records-section" data-bs-target="#appointment" role="button" aria-controls="appointment" aria-selected="true">Appointments</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="prenatalInfo-tab" data-bs-toggle="pill" href="#records-section" data-bs-target="#prenatalInfo" role="button" aria-controls="prenatalInfo" aria-selected="true">Prenatal Information</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="otherInfo-tab" data-bs-toggle="pill" href="#records-section" data-bs-target="#otherInfo" role="button" aria-controls="otherInfo" aria-selected="false">Other Information</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="trimesters-tab" data-bs-toggle="pill" href="#records-section" data-bs-target="#trimesters" role="button" aria-controls="trimesters" aria-selected="false">Trimesters</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="appointment" role="tabpanel" aria-labelledby="appointment-tab" tabindex="0">
                            <div class="tab-pane-content">
                                <div class="row">
                                <?php
                                    $count = 0;
                                    // Loop through the referrals and display each patient in a table row
                                    foreach ($getAppointments as $key => $data) {
                                        $count++;
                                        $schedID = $data['schedule_id'];
                                        $patient_id = $data['patients_id'];
                                        $date = $data['date'];
                                        $trimester = $data['trimester'];
                                        $check_up = $data['check_up'];
                                        $record = $data['record'];
                                        $status = $data['status'];

                                        $dateTime = new DateTime($date);

                                        $month = $dateTime->format('M');
                                        $day = $dateTime->format('j');
                                        $year = $dateTime->format('Y');

                                        ?>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="card shadow">
                                            <div class="card-header">
                                                <div class="status"><?php echo $status ?></div>
                                                <div class="status-date"><?php echo $year ?></div>
                                            </div>
                                            <div class="card-body">
                                                <div class="date">
                                                    <h1><?php echo $day ?></h1>
                                                    <h6><?php echo $month ?></h6>
                                                </div>
                                                <div class="content">
                                                    <p><?php echo $trimester ?></p>
                                                    <p><?php echo $check_up ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                        if ($count == 0) {
                                            echo "<tr><td colspan='6'>No records found</td></tr>";
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="prenatalInfo" role="tabpanel" aria-labelledby="prenatalInfo-tab" tabindex="0">
                            <div class="tab-pane-content">
                                <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Date of first Checkup</label>
                                            <input type="text" name="petsa_ng_unang_checkup" id="petsa_ng_unang_checkup" class="form-control" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Age</label>
                                            <input type="number" class="form-control" readonly name="edad" id="edad" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Weight</label>
                                            <input type="text" class="form-control" readonly name="timbang" id="timbang" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Height</label>
                                            <input type="text" class="form-control" readonly name="taas" id="taas" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Health Status</label>
                                            <input type="text" class="form-control" readonly name="kalagayan_ng_kalusugan" id="kalagayan_ng_kalusugan" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Date of last Period</label>
                                            <input type="text" name="petsa_ng_huling_regla" id="petsa_ng_huling_regla" class="form-control" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>When to give Birth</label>
                                            <input type="text" name="kailan_ako_manganganak" id="kailan_ako_manganganak" class="form-control" readonly>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                            <label>Birth Count</label>
                                            <input type="number" class="form-control" name="pang_ilang_pagbubuntis" id="pang_ilang_pagbubuntis" readonly>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="otherInfo" role="tabpanel" aria-labelledby="otherInfo-tab" tabindex="0">
                            <div class="tab-pane-content">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <label>Date of delivery</label>
                                        <input type="text" name="date_of_delivery" id="date_of_delivery" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <label>Type of delivery</label>
                                        <input type="text" name="type_of_delivery" id="type_of_delivery" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <label>Birth Outcome</label>
                                        <input type="text" name="birth_outcome" id="birth_outcome" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <label>Number of Child / Children delivered</label>
                                        <input type="text" name="number_of_children_delivered" id="number_of_children_delivered" class="form-control" readonly>
                                    </div>
                                    <div class="col-12" style="margin-bottom: 15px;  margin-top: 10px">
                                        <h5>Pregnancy-related Conditions / Complications</h5>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                        <label>Pregnancy Included Hypertension (PIH)</label>
                                        <input type="text" name="pregnancy_hypertension" id="pregnancy_hypertension" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                        <label>Preeclampsia / Eclampsia (PE/E)</label>
                                        <input type="text" name="preeclampsia_eclampsia" id="preeclampsia_eclampsia" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                        <label>Bleeding during pregnancy or after delivery</label>
                                        <input type="text" name="bleeding_during_pregnancy" id="bleeding_during_pregnancy" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="trimesters" role="tabpanel" aria-labelledby="trimesters-tab" tabindex="0">
                            <div class="nav-tabs-header-trimester">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a role="button" class="nav-link active left-button" id="pills-home-tab" data-bs-toggle="pill" data-tab="first_trimester" data-bs-target="#pills-home" type="button" href="#records-section" aria-controls="pills-home" aria-selected="true">First Trimester</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a role="button" class="nav-link left-button"  id="pills-profile-tab" data-bs-toggle="pill" data-tab="second_trimester" data-bs-target="#pills-profile" type="button" href="#records-section" aria-controls="pills-profile" aria-selected="false">Second Trimester</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a role="button" class="nav-link left-button" id="pills-contact-tab" data-bs-toggle="pill" data-tab="third_trimester" data-bs-target="#pills-contact" type="button" href="#records-section" aria-controls="pills-contact" aria-selected="false">Third Trimester</a>
                                    </li>
                                </ul>
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active right-button" id="pills-home-tab" data-bs-toggle="pill" data-tab="first_checkup" type="button" role="tab" aria-controls="pills-home" aria-selected="true">First Check up</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link right-button" id="pills-profile-tab"  data-bs-toggle="pill" data-tab="second_checkup" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Second Check up</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link right-button" id="pills-contact-tab" data-bs-toggle="pill" data-tab="third_checkup" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Third Check up</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content p-2" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                    <div class="tab-pane-content">
                                        <div class="row trimester-form">
                                            <div class="col-sm-4 mb-3">
                                                <label>Date</label>
                                                <input type="text" name="firstTri_date" id="firstTri_date" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Weight</label>
                                                <input type="text" name="firstTri_weight" id="firstTri_weight" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Height</label>
                                                <input type="text" name="firstTri_height" id="firstTri_height" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Age of Gestation</label>
                                                <input type="text" name="firstTri_age_of_gestation" id="firstTri_age_of_gestation" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Blood Pressure</label>
                                                <input type="text" name="firstTri_blood_pressure" id="firstTri_blood_pressure" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Nutritional Status</label>
                                                <input type="text" name="firstTri_nutritional_status" id="firstTri_nutritional_status" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Laboratory Tests Done</label>
                                                <input type="text" name="firstTri_laboratory_tests_done" id="firstTri_laboratory_tests_done" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Hemoglobin Count</label>
                                                <input type="text" name="firstTri_hemoglobin_count" id="firstTri_hemoglobin_count" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Urinalysis</label>
                                                <input type="text" name="firstTri_urinalysis" id="firstTri_urinalysis" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Complete Blood Count (CBC)</label>
                                                <input type="text" name="firstTri_complete_blood_count" id="firstTri_complete_blood_count" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>STIs using a syndromic approach</label>
                                                <input type="text" name="firstTri_stis_using_a_syndromic_approach" id="firstTri_stis_using_a_syndromic_approach" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Tetanus-containing Vaccine</label>
                                                <input type="text" name="firstTri_tetanus_containing_vaccine" id="firstTri_tetanus_containing_vaccine" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label for="hospital_referral">Given Services</label>
                                                <input type="text" name="firstTri_given_services" id="firstTri_given_services" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Date of Return</label>
                                                <input type="text" name="firstTri_date_of_return" id="firstTri_date_of_return" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Health Provider Name</label>
                                                <input type="text" name="firstTri_health_provider_name" id="firstTri_health_provider_name" class="form-control" readonly> 
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label for="hospital_referral">Hospital Referral</label>
                                                <input type="text" name="firstTri_hospital_referral" id="firstTri_hospital_referral" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                    <div class="tab-pane-content">
                                        <div class="row trimester-form">
                                            <div class="col-sm-4 mb-3">
                                                <label>Date</label>
                                                <input type="text" name="secondTri_date" id="secondTri_date" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Weight</label>
                                                <input type="text" name="secondTri_weight" id="secondTri_weight" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Height</label>
                                                <input type="text" name="secondTri_height" id="secondTri_height" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Age of Gestation</label>
                                                <input type="text" name="secondTri_age_of_gestation" id="secondTri_age_of_gestation" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Blood Pressure</label>
                                                <input type="text" name="secondTri_blood_pressure" id="secondTri_blood_pressure" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Nutritional Status</label>
                                                <input type="text" name="secondTri_nutritional_status" id="secondTri_nutritional_status" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Given Advise</label>
                                                <input type="text" name="secondTri_given_advise" id="secondTri_given_advise" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Laboratory Tests Done</label>
                                                <input type="text" name="secondTri_laboratory_tests_done" id="secondTri_laboratory_tests_done" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Urinalysis</label>
                                                <input type="text" name="secondTri_urinalysis" id="secondTri_urinalysis" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Complete Blood Count (CBC)</label>
                                                <input type="text" name="secondTri_complete_blood_count" id="secondTri_complete_blood_count" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Given Services</label>
                                                <input type="text" name="secondTri_given_services" id="secondTri_given_services" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Date of Return</label>
                                                <input type="text" name="secondTri_date_of_return" id="secondTri_date_of_return" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Health Provider Name</label>
                                                <input type="text" name="secondTri_health_provider_name" id="secondTri_health_provider_name" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label for="hospital_referral">Hospital Referral</label>
                                                <input type="text" name="secondTri_hospital_referral" id="secondTri_hospital_referral" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                                    <div class="tab-pane-content">
                                        <div class="row trimester-form">
                                            <div class="col-sm-4 mb-3">
                                                <label>Date</label>
                                                <input type="text" name="thirdTri_date" id="thirdTri_date" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Weight</label>
                                                <input type="text" name="thirdTri_weight" id="thirdTri_weight" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Height</label>
                                                <input type="text" name="thirdTri_height" id="thirdTri_height" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Age of Gestation</label>
                                                <input type="text" name="thirdTri_age_of_gestation" id="thirdTri_age_of_gestation" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Blood Pressure</label>
                                                <input type="text" name="thirdTri_blood_pressure" id="thirdTri_blood_pressure" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Nutritional Status</label>
                                                <input type="text" name="thirdTri_nutritional_status" id="thirdTri_nutritional_status" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Given Advise</label>
                                                <input type="text" name="thirdTri_given_advise" id="thirdTri_given_advise" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Laboratory Tests Done</label>
                                                <input type="text" name="thirdTri_laboratory_tests_done" id="thirdTri_laboratory_tests_done" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Urinalysis</label>
                                                <input type="text" name="thirdTri_urinalysis" id="thirdTri_urinalysis" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Complete Blood Count (CBC)</label>
                                                <input type="text" name="thirdTri_complete_blood_count" id="thirdTri_complete_blood_count" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label for="hospital_referral">Given Services</label>
                                                <input type="text" name="thirdTri_given_services" id="thirdTri_given_services" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Date of Return</label>
                                                <input type="text" name="thirdTri_date_of_return" id="thirdTri_date_of_return" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label>Health Provider Name</label>
                                                <input type="text" name="thirdTri_health_provider_name" id="thirdTri_health_provider_name" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label for="hospital_referral">Hospital Referral</label>
                                                <input type="text" name="thirdTri_hospital_referral" id="thirdTri_hospital_referral" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4 mb-2">
        <div class="bg-light-subtle files-section shadow-sm" id="filesDIv">
            <div class="files-header">
                <h6>Files / Documents</h6>
                <button type="button" class="btn" id="fileButton"><i class="fi fi-rr-add-document"></i> Add Files</button>
                <input type="file" id="attachment" style="display: none;">
            </div>
            <?php
            if ($getAttachments) {
                foreach ($getAttachments as $data) {
                    $filename = $data['filename'];
                    $patients_id = $data['patients_id'];
                    $unique_filename = $data['unique_filename'];
                    $file_path = '../../attachments/' . $unique_filename; // Set the correct path to your files
                    $attachments_id  = $data['attachments_id'];

            ?>
                    <div class="files shadow-sm mb-3">
                        <i class="fi fi-rr-document"></i>
                        <span><?php echo $filename; ?></span>
                        <div class="file-buttons">
                        <a href="<?php echo $file_path; ?>" download class="btn-icon">
                            <i class="fi fi-bs-arrow-circle-down"></i>
                        </a>
                        <button class="btn-icon deleteAttachment" value="<?php echo $attachments_id; ?>"><i class="fi fi-br-trash"></i></button>
                        </div>
                    </div>
                    
            <?php
                }
            } else {
                echo "<h6>No attachments found.</h6>";
            }
            ?>
        </div>
    </div>
</div>
</div>

<script>
$(document).ready(function() {
  // Add smooth scrolling to all links inside the navbar
  $(".nav-pills a").on('click', function(event) {
    // Prevent default anchor click behavior
    event.preventDefault();

    // Store the hash
    var hash = this.hash;

    // Animate scroll to the target section
    $('html, body').animate({
      scrollTop: $(hash).offset().top
    }, 50, function(){
      // Add hash (#) to URL when done scrolling (default click behavior)
      window.location.hash = hash;
    });
  });
});
</script>
<script src="js/ajaxViewPatients.js"></script>

<?php
include_once 'footer.php';
?>