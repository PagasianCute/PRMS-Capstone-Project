<?php
ob_start(); // Start output buffering

require_once 'header.php';
require_once '../../fpdf186/fpdf.php';

// Function to generate PDF
function generatePDF($conn, $dateFrom, $dateTo, $selectedHospital, $selectedStatus) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 10, 'ID', 1);
    $pdf->Cell(40, 10, 'Date', 1);
    $pdf->Cell(70, 10, 'Referred Hospital', 1);
    $pdf->Cell(40, 10, 'Status', 1);

    // Fetch data from the database based on criteria
    $stmt = $conn->prepare("SELECT * FROM referral_records INNER JOIN facilities ON referral_records.referred_hospital = facilities.fclt_id WHERE referral_records.date BETWEEN ? AND ? AND referred_hospital = ? AND referral_records.status = ?");
    $stmt->bind_param("ssss", $dateFrom, $dateTo, $selectedHospital, $selectedStatus);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $pdf->Ln();
            $pdf->Cell(40, 10, $row["id"], 1);
            $pdf->Cell(40, 10, $row["date"], 1);
            $pdf->Cell(70, 10, $row["fclt_name"], 1);
            $pdf->Cell(40, 10, $row["status"], 1);
        }
    } else {
        // Handle the error
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    $pdf->Output();
}

// Check if the required parameters are set
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["date"])) {
    $dateFrom = $_GET["date"];
    $dateTo = $_GET["date2"];
    $selectedHospital = $_GET["selectedHospital"];
    $selectedStatus = isset($_GET["status"]) ? $_GET["status"] : null;

    // Call the function to generate PDF
    generatePDF($conn, $dateFrom, $dateTo, $selectedHospital, $selectedStatus);
}

include_once 'footer.php';

ob_end_flush(); // Send the buffer and turn off output buffering
?>
