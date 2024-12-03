<?php
require_once '../../../fpdf186/fpdf.php';
require_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];
$fclt_type = $_SESSION["fclttype"];

function generateReport($date_from, $date_to, $status)
{
    global $conn, $fclt_id;

    if ($_SESSION['fclttype'] == 'Birthing Home') {
        $sql = "SELECT * FROM referral_records INNER JOIN facilities ON referral_records.referred_hospital = facilities.fclt_id WHERE referral_records.date BETWEEN '$date_from' AND '$date_to'";
    } else {
        $sql = "SELECT referral_transaction.*, referral_records.fclt_id, facilities.fclt_name FROM referral_transaction INNER JOIN referral_records ON referral_transaction.rfrrl_id = referral_records.rfrrl_id
                INNER JOIN facilities ON referral_records.fclt_id = facilities.fclt_id WHERE referral_transaction.date BETWEEN '$date_from' AND '$date_to' AND referral_transaction.fclt_id = $fclt_id";
    }

    // Check if $status is not empty before adding it to the query
    if (!empty($status) || $status != '') {
        if ($_SESSION['fclttype'] == 'Birthing Home') {
            $sql .= " AND referral_records.status = '$status'";
        } else {
            $sql .= " AND referral_transaction.status = '$status'";
        }
    }

    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result;
}

// Check if 'id' parameter is present in the URL
if (isset($_GET['date_from']) && isset($_GET['date_to']) && isset($_GET['status'])) {
    global $conn;
    $date_from = $_GET['date_from'];
    $date_to = $_GET['date_to'];
    $status = $_GET['status'];
    $result = generateReport($date_from, $date_to, $status);

    if ($result->num_rows > 0) {
        ob_start();
        // PDF generation code
        $pdf = new FPDF('L', 'mm', 'A4'); // Set to landscape orientation
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 20); // Adjust font size for title
        $pdf->Cell(0, 10, 'Referral Report', 0, 1, 'C'); // Add title

        $pdf->SetFont('Arial', 'I', 12); // Adjust font size for total records count
        $pdf->Cell(0, 10, 'Total Records: ' . $result->num_rows, 0, 1, 'C'); // Add total records count
        $pdf->Ln(10); // Add some space after the title

        $pdf->SetFont('Arial', 'B', 16); // Adjust font size

        // Calculate left margin to center the table
        $leftMargin = ($pdf->GetPageWidth() - $pdf->GetStringWidth('Referral Report')) / 7.5;
        $pdf->SetLeftMargin($leftMargin);

        // Output column headers with adjusted width
        $pdf->Cell(15, 10, 'No.', 1, 0, 'C');
        if ($_SESSION['fclttype'] == 'Birthing Home') {
            $pdf->Cell(80, 10, 'Referred Facility', 1, 0, 'C');
        } else {
            $pdf->Cell(80, 10, 'Referring Facility', 1, 0, 'C');
        }
        $pdf->Cell(35, 10, 'Referral ID', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Date', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Time', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Status', 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12); // Reset font size for data rows

        // Loop through the rows and output data
        $counter = 1;
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(15, 10, $counter, 1, 0, 'C');
            $pdf->Cell(80, 10, $row['fclt_name'], 1, 0, 'L');
            $pdf->Cell(35, 10, $row['rfrrl_id'], 1, 0, 'L');
            $pdf->Cell(30, 10, $row['date'], 1, 0, 'L');
            $pdf->Cell(30, 10, date('h:i A', strtotime($row['time'])), 1, 0, 'L');
            $pdf->Cell(40, 10, $row['status'], 1, 0, 'L');
            $pdf->Ln();
            $counter++;
        }

        $pdf->Output();
    } else {
        echo "No records found for the specified criteria.";
    }
} else {
    // Invalid request
    echo "Invalid request. Please select a patient.";
}
?>
