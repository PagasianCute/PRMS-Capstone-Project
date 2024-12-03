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

function firstTrimesterFunction($id,$rec) {
    global $conn;

    $query = "SELECT * FROM first_trimester WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function secondTrimesterFunction($id,$rec) {
    global $conn;

    $query = "SELECT * FROM second_trimester WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

function thirdTrimesterFunction($id,$rec) {
    global $conn;

    $query = "SELECT * FROM third_trimester WHERE patients_id = $id AND records_count = $rec";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result->fetch_assoc();
}

// Check if 'id' parameter is present in the URL
if (isset($_GET['id']) && isset($_GET['record'])) {
    global $conn;
    $id = $_GET['id'];
    $rec = $_GET['record'];
    $info = infoFunction($id);
    $record = recordFunction($id,$rec);
    $details = detailsFunction  ($id,$rec);
    $birthExp = birthExperienceFunction($id,$rec);
    $firstTri = firstTrimesterFunction($id,$rec);
    $secondTri = secondTrimesterFunction($id,$rec);
    $thirdTri = thirdTrimesterFunction($id,$rec);

        
        ob_start();
        // PDF generation code
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, 'Patient Data', 0, 1, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);

        // Display patient data in the PDF
        $pdf->Cell(52, 15, 'Date of First Checkup: '  . ($details['petsa_ng_unang_checkup'] ?? '(empty)'), 0, 0, 'L');
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

        
        
        
        // Output the PDF
        
        
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 10, 'Trimester Records', 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        
            // Table Header
            $pdf->Cell(65, 10, 'First Trimester', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Second Trimester', 1, 0, 'C');
            $pdf->Cell(65, 10, 'Third Trimester', 1, 0, 'C');
            $pdf->Ln();
            
            // Row 1
            $pdf->Cell(65, 10, 'Date: ' . ($firstTri['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($secondTri['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date: ' . ($thirdTri['date'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 2 (Empty)
            $pdf->Cell(65, 10, 'Weight: ' . ($firstTri['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($secondTri['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Weight: ' . ($thirdTri['weight'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 3
            $pdf->Cell(65, 10, 'Height: ' . ($firstTri['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($secondTri['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Height: ' . ($thirdTri['height'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 4
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($firstTri['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($secondTri['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Age of Gestation: ' . ($thirdTri['age_of_gestation'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 5
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($firstTri['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($secondTri['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'BloodPressure: ' . ($thirdTri['blood_pressure'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 6
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($firstTri['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($secondTri['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Nutritional Status: ' . ($thirdTri['nutritional_status'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Ln();
            
            // Row 7
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($firstTri['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Advise: ' . ($secondTri['given_advise'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done:', 1, 0, 'L');
            $pdf->Ln();
        
            $pdf->Cell(65, 10, 'Hemoglobin Count: ' . ($firstTri['hemoglobin_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Laboratory Test Done: ' . ($secondTri['laboratory_tests_done'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Hemoglobin Count', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($firstTri['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis: ' . ($secondTri['urinalysis'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Uranilysis', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($firstTri['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC): ' . ($secondTri['complete_blood_count'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Complete Blood Count (CBC)', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'STIs using a syndromic approach: ' . ($firstTri['stis_using_a_syndromic_approach'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services: ' . ($secondTri['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'STIs using a syndromic approach:', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Tetanus-Containing Vaccine: ' . ($firstTri['tetanus_containing_vaccine'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return: ' . ($secondTri['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Tetanus-Containing Vaccine:', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Given Services: ' . ($firstTri['given_services'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($secondTri['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Given Services:', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Date of Return: ' . ($firstTri['date_of_return'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($secondTri['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Date of Return:', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Health Provider Name: ' . ($firstTri['health_provider_name'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name', 1, 0, 'L');
            $pdf->Cell(65, 10, 'Health Provider Name', 1, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(65, 10, 'Referred Hospital: ' . ($firstTri['hospital_referral'] ?? '(empty)'), 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ', 1, 0, 'L');
            $pdf->Cell(65, 10, 'Referred Hospital: ', 1, 0, 'L');
            $pdf->Ln();
         
         
        
        
        // Add more fields as needed

        $pdf->Output();
} else {
    // Invalid request
    echo "Invalid request. Please select a patient.";
}
?>
