<?php
require_once '../../../fpdf186/fpdf.php';
require_once '../../../db/db_conn.php';

function infoFunction($id) {
    global $conn;

    $query = "SELECT * FROM patients WHERE id = $id";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function detailsFunction($id,$rec) {
    global $conn;

    $query = "SELECT * FROM patients_details WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function birthExperienceFunction($id,$rec) {
    global $conn;

    $query = "SELECT * FROM birth_experience WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function recordFunction($id,$rec) {
    global $conn;

    $query = "SELECT * FROM prenatal_records WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function executeCheckups($id, $rec, $trimester)
{
    global $conn;

    $firstCheckup = firstCheckupFunction($id, $rec, $trimester);
    $secondCheckup = secondCheckupFunction($id, $rec, $trimester);
    $thirdCheckup = thirdCheckupFunction($id, $rec, $trimester);

    return array($firstCheckup, $secondCheckup, $thirdCheckup);
}

function firstTrimesterFunction($id,$rec,$checkup) {
    global $conn;

    $query = "SELECT * FROM first_trimester WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function secondTrimesterFunction($id,$rec,$checkup) {
    global $conn;

    $query = "SELECT * FROM second_trimester WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function thirdTrimesterFunction($id,$rec,$checkup) {
    global $conn;

    $query = "SELECT * FROM third_trimester WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function firstCheckupFunction($id,$rec,$trimester) {
    global $conn;
    $checkup = "first_checkup";

    $query = "SELECT * FROM $trimester WHERE patients_id = $id AND records_count = $rec AND check_up = '$checkup'";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function secondCheckupFunction($id,$rec,$trimester) {
    global $conn;
    $checkup = "second_checkup";

    $query = "SELECT * FROM $trimester WHERE patients_id = $id AND records_count = $rec AND check_up = '$checkup'";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function thirdCheckupFunction($id,$rec,$trimester) {
    global $conn;
    $checkup = "third_checkup";

    $query = "SELECT * FROM $trimester WHERE patients_id = $id AND records_count = $rec AND check_up = '$checkup'";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

// Check if 'id' parameter is present in the URL
if (isset($_GET['id']) && isset($_GET['record']) && isset($_GET['checkup']) && isset($_GET['trimester'])) {
    global $conn;
    $id = $_GET['id'];
    $rec = $_GET['record'];
    $checkup = $_GET['checkup'];
    $trimester = $_GET['trimester'];
    $info = infoFunction($id);
    $record = recordFunction($id,$rec);
    $details = detailsFunction  ($id,$rec);
    $birthExp = birthExperienceFunction($id,$rec);
    $firstTriCheckups = executeCheckups($id, $rec, 'first_trimester');
    $secondTriCheckups = executeCheckups($id, $rec, 'second_trimester');
    $thirdTriCheckups = executeCheckups($id, $rec, 'third_trimester');

        
        ob_start();
        // PDF generation code
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, 'Patient Data', 0, 1, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);

        $checkupLabel = '';

        if ($checkup == 'first_checkup') {
            $checkupLabel = 'Date of First Checkup: ';
        } elseif ($checkup == 'second_checkup') {
            $checkupLabel = 'Date of Second Checkup: ';
        } elseif ($checkup == 'third_checkup') {
            $checkupLabel = 'Date of Third Checkup: ';
        } else {
            // Handle the case where $checkup doesn't match any expected value
            $checkupLabel = 'Unknown Checkup: ';
        }

        // Display patient data in the PDF
        $pdf->Cell(52, 15, $checkupLabel  . ($details['petsa_ng_unang_checkup'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'Names: ' . ($info['lname'] ?? '(empty)') . ', ' . ($info['fname'] ?? '(empty)') . ' ' . ($info['mname'] ?? '(empty)'), 0, 1, 'L');
        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(80, 5, 'Age: ' . ($details['edad'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(15, 5, '', 0, 0, 'L');
        $pdf->Cell(15, 5, 'Weight: ' . ($details['timbang'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(15, 5, 'Height: ' . ($details['taas'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(33, 15, 'Health Status: ' . ($details['kalagayan_ng_kalusugan'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Cell(25, 15, '', 0, 0, 'L');
        $pdf->Cell(120, 15, 'Date of Last Period: ' . ($details['petsa_ng_huling_regla'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(15, 15, '', 0, 0, 'L');
        $pdf->Ln();

        $pdf->Cell(45, 10, 'When to give Birth: ' . ($details['kailan_ako_manganganak'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Cell(80, 10, ' ', 0, 0, 'L');

        $pdf->Cell(45, 10, 'Birth Count: ' . ($details['pang_ilang_pagbubuntis'] ?? '(empty)'), 0, 0, 'C');
        $pdf->Cell(10, 10, '', 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(45, 10, 'Record Count: ' . ($record['records_count'] ?? '(empty)'), 0, 1, 'L');
        $pdf->Cell(20, 5, '', 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(130, 30, 'Karanasan sa ma Naunang Pagbubuntis at Panganganak', 0, 0, 'L');
        $pdf->LN();
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(33, 5, 'Date of Delivery: ' . ($birthExp['date_of_delivery'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Cell(25, 5, '', 0, 0, 'L');
        $pdf->Cell(110, 5, 'Type of delivery: ' . ($birthExp['type_of_delivery'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(15, 5, '', 0, 0, 'L');
        $pdf->LN();
        $pdf->Cell(33, 10, 'Birth Outcome: ' . ($birthExp['birth_outcome'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Cell(25, 10, '', 0, 0, 'L');
        $pdf->Cell(100, 5, 'Number of Child Delivered: ' . ($birthExp['number_of_children_delivered'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(15, 5, '', 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(130, 30, 'Pregnancy related Conditions/Complications', 0, 0, 'L');
        $pdf->LN();
        $pdf->SetFont('Arial', '', 13);
        $pdf->Cell(20, 5, 'Pregnancy Included Hypertension: ' . ($birthExp['pregnancy_hypertension'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Cell(65, 5, '', 0, 0, 'R');
        $pdf->Cell(100, 5, 'Preeclampsia/Eclampsia: ' . ($birthExp['preeclampsia_eclampsia'] ?? '(empty)'), 0, 0, 'R');
        $pdf->Cell(15, 5, '', 0, 0, 'L');

        $pdf->Ln();
        $pdf->Cell(20, 10, 'Bleeding during or after Delivery: ' . ($birthExp['bleeding_during_pregnancy'] ?? '(empty)'), 0, 0, 'L');
        $pdf->Cell(65, 10, '', 0, 0, 'R');

        
        
        
            // Output the PDF for First trimester

            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 10, 'First Trimester Records', 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        
            // Table Header
            $pdf->Cell(65, 10, 'First Checkup', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Second Checkup', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Third Checkup', 1, 0, 'C');
            $pdf->Ln();
            
            // Row 1
            $pdf->Cell(65, 10, 'Date: ' . ($firstTriCheckups[0]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($firstTriCheckups[1]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($firstTriCheckups[2]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 2 
            $pdf->Cell(65, 10, 'Weight: ' . ($firstTriCheckups[0]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($firstTriCheckups[1]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($firstTriCheckups[2]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 3
            $pdf->Cell(65, 10, 'Height: ' . ($firstTriCheckups[0]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($firstTriCheckups[1]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($firstTriCheckups[2]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 4
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($firstTriCheckups[0]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($firstTriCheckups[1]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($firstTriCheckups[2]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 5
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($firstTriCheckups[0]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($firstTriCheckups[1]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($firstTriCheckups[2]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 6
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($firstTriCheckups[0]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($firstTriCheckups[1]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($firstTriCheckups[2]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 7
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($firstTriCheckups[0]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($firstTriCheckups[1]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($firstTriCheckups[2]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
        
            $pdf->Cell(65, 10, 'Hemoglobin Count: ' . ($firstTriCheckups[0]['hemoglobin_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Hemoglobin Count: ' . ($firstTriCheckups[1]['hemoglobin_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Hemoglobin Count: ' . ($firstTriCheckups[2]['hemoglobin_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 8
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($firstTriCheckups[0]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($firstTriCheckups[1]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($firstTriCheckups[2]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 9
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($firstTriCheckups[0]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($firstTriCheckups[1]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($firstTriCheckups[2]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 10
            $pdf->Cell(65, 10, 'STIs using a syndromic approach: ' . ($firstTriCheckups[0]['stis_using_a_syndromic_approach'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'STIs using a syndromic approach: ' . ($firstTriCheckups[1]['stis_using_a_syndromic_approach'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'STIs using a syndromic approach: ' . ($firstTriCheckups[2]['stis_using_a_syndromic_approach'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 11
            $pdf->Cell(65, 10, 'Tetanus-Containing Vaccine: ' . ($firstTriCheckups[0]['tetanus_containing_vaccine'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Tetanus-Containing Vaccine: ' . ($firstTriCheckups[1]['tetanus_containing_vaccine'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Tetanus-Containing Vaccine: ' . ($firstTriCheckups[2]['tetanus_containing_vaccine'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 12
            $pdf->Cell(65, 10, 'Given Services: ' . ($firstTriCheckups[0]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($firstTriCheckups[1]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($firstTriCheckups[2]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 13
            $pdf->Cell(65, 10, 'Date of Return: ' . ($firstTriCheckups[0]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($firstTriCheckups[1]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($firstTriCheckups[2]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 14
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($firstTriCheckups[0]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($firstTriCheckups[1]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($firstTriCheckups[2]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 15
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($firstTriCheckups[0]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($firstTriCheckups[1]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($firstTriCheckups[2]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();


            // Output the PDF for Srcond trimester

            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 10, 'Second Trimester Records', 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        
            // Table Header
            $pdf->Cell(65, 10, 'First Checkup', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Second Checkup', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Third Checkup', 1, 0, 'C');
            $pdf->Ln();
            
            // Row 1
            $pdf->Cell(65, 10, 'Date: ' . ($secondTriCheckups[0]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($secondTriCheckups[1]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($secondTriCheckups[2]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 2 
            $pdf->Cell(65, 10, 'Weight: ' . ($secondTriCheckups[0]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($secondTriCheckups[1]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($secondTriCheckups[2]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 3
            $pdf->Cell(65, 10, 'Height: ' . ($secondTriCheckups[0]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($secondTriCheckups[1]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($secondTriCheckups[2]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 4
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($secondTriCheckups[0]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($secondTriCheckups[1]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($secondTriCheckups[2]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 5
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($secondTriCheckups[0]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($secondTriCheckups[1]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($secondTriCheckups[2]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 6
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTriCheckups[0]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTriCheckups[1]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTriCheckups[2]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 6 insert
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTriCheckups[0]['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTriCheckups[1]['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTriCheckups[2]['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 7
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($secondTriCheckups[0]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($secondTriCheckups[1]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($secondTriCheckups[2]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
    

            // Row 8
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($secondTriCheckups[0]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($secondTriCheckups[1]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($secondTriCheckups[2]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 9
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($secondTriCheckups[0]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($secondTriCheckups[1]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($secondTriCheckups[2]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 12
            $pdf->Cell(65, 10, 'Given Services: ' . ($secondTriCheckups[0]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($secondTriCheckups[1]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($secondTriCheckups[2]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 13
            $pdf->Cell(65, 10, 'Date of Return: ' . ($secondTriCheckups[0]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($secondTriCheckups[1]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($secondTriCheckups[2]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 14
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($secondTriCheckups[0]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($secondTriCheckups[1]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($secondTriCheckups[2]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 15
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($secondTriCheckups[0]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($secondTriCheckups[1]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($secondTriCheckups[2]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();


            // Output the PDF for Third trimester

            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 10, 'Third Trimester Records', 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        
            // Table Header
            $pdf->Cell(65, 10, 'First Checkup', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Second Checkup', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Third Checkup', 1, 0, 'C');
            $pdf->Ln();
            
            // Row 1
            $pdf->Cell(65, 10, 'Date: ' . ($thirdTriCheckups[0]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($thirdTriCheckups[1]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($thirdTriCheckups[2]['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 2 
            $pdf->Cell(65, 10, 'Weight: ' . ($thirdTriCheckups[0]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($thirdTriCheckups[1]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($thirdTriCheckups[2]['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 3
            $pdf->Cell(65, 10, 'Height: ' . ($thirdTriCheckups[0]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($thirdTriCheckups[1]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($thirdTriCheckups[2]['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 4
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($thirdTriCheckups[0]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($thirdTriCheckups[1]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($thirdTriCheckups[2]['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 5
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($thirdTriCheckups[0]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($thirdTriCheckups[1]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($thirdTriCheckups[2]['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 6
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTriCheckups[0]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTriCheckups[1]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTriCheckups[2]['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 6 insert
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTriCheckups[0]['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTriCheckups[1]['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTriCheckups[2]['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 7
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($thirdTriCheckups[0]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($thirdTriCheckups[1]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($thirdTriCheckups[2]['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 8
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($thirdTriCheckups[0]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($thirdTriCheckups[1]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($thirdTriCheckups[2]['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 9
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($thirdTriCheckups[0]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($thirdTriCheckups[1]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($thirdTriCheckups[2]['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 12
            $pdf->Cell(65, 10, 'Given Services: ' . ($thirdTriCheckups[0]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($thirdTriCheckups[1]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($thirdTriCheckups[2]['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 13
            $pdf->Cell(65, 10, 'Date of Return: ' . ($thirdTriCheckups[0]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($thirdTriCheckups[1]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($thirdTriCheckups[2]['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 14
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($thirdTriCheckups[0]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($thirdTriCheckups[1]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($thirdTriCheckups[2]['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();

            // Row 15
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($thirdTriCheckups[0]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($thirdTriCheckups[1]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($thirdTriCheckups[2]['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
         
         

        $pdf->Output();
} else {
    // Invalid request
    echo "Invalid request. Please select a patient.";
}
?>
