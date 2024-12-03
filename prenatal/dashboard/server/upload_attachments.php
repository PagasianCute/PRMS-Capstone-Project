<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$fclt_name = $_SESSION["fcltname"];

$uploadDir = '../../../attachments/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $files = $_FILES['files'];
    $fileResponses = array();
    $patients_id = $_POST['patients_id'];

    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = $files['name'][$i];
        $fileTmpPath = $files['tmp_name'][$i];
        $fileSize = $files['size'][$i];

        // Check file extension for PDF files
        $allowedExtensions = array('pdf');
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $fileResponses[] = 'Error: Invalid file format for file "' . $fileName . '"';
            continue;
        }

        // Limit file size to, for example, 5 MB
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($fileSize > $maxFileSize) {
            $fileResponses[] = 'Error: File "' . $fileName . '" exceeds the maximum allowed size';
            continue;
        }

        // Generate a unique filename to prevent overwriting
        $uniqueFileName = uniqid() . '_' . $fileName;
        $filePath = $uploadDir . $uniqueFileName;

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            // File upload successful
            $sql = "INSERT INTO patients_attachments (filename, unique_filename, patients_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            // Use bind_param to prevent SQL injection
            mysqli_stmt_bind_param($stmt, "ssi", $fileName, $uniqueFileName, $patients_id);

            if (mysqli_stmt_execute($stmt)) {
                $fileResponses[] = 'File "' . $fileName . '" uploaded and database record inserted successfully';
            } else {
                $fileResponses[] = 'Error inserting record into database for file "' . $fileName . '": ' . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        } else {
            // File upload failed
            $fileResponses[] = 'Error uploading file "' . $fileName . '"';
        }
    }

    echo json_encode($fileResponses);
} else {
    // Invalid request
    echo 'Invalid request';
}

mysqli_close($conn);
?>
