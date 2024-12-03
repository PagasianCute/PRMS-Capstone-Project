<?php

include_once '../../../db/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the selected data from the AJAX request
    $fclt_name = $_POST['fclt_name'] ?? [];
    $status = $_POST['status'] ?? [];

    $fclt_name_placeholder = implode(",", $fclt_name);
    $status_placeholder = implode(",", $status);

    // Check if either status or fclt_name is empty or null
    if (empty($status) && empty($fclt_name)) {
        echo 'No data selected';
    } else {
        // Initialize an array to store the fetched data
        $fetchedData = [];

        // Process the selected data as needed
        // You can perform database queries or other actions based on the selected data

        // Fetch data based on fclt_name
        if (!empty($fclt_name) && !empty($status)) {
            // Construct the SQL query
            $query = "SELECT referral_forms.*, referral_records.*, facilities.*
                FROM referral_forms
                INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
                INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital 
                WHERE fclt_name IN ($fclt_name_placeholder) AND status IN ($status_placeholder)";
            $query_run = mysqli_query($conn, $query);
        } else if (!empty($fclt_name)) {
            // Construct the SQL query
            $query = "SELECT referral_forms.*, referral_records.*, facilities.*
                FROM referral_forms
                INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
                INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital 
                WHERE fclt_name IN ($fclt_name_placeholder)";
            $query_run = mysqli_query($conn, $query);
        } else if (!empty($status)) {
            // Construct the SQL query
            $query = "SELECT referral_forms.*, referral_records.*, facilities.*
                FROM referral_forms
                INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
                INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital 
                WHERE status IN ($status_placeholder)";
            $query_run = mysqli_query($conn, $query);
        }

        if ($query_run) {
            // Fetch data and store it in the $fetchedData array
            while ($row = mysqli_fetch_assoc($query_run)) {
                $fetchedData[] = $row;
            }

            if (count($fetchedData) > 0) {
                $res = [
                    'status' => 200,
                    'message' => 'Data fetched',
                    'data' => $fetchedData
                ];
            } else {
                $res = [
                    'status' => 404,
                    'message' => 'No data found',
                ];
            }

            echo json_encode($res);
            return false;
        } else {
            $res = [
                'status' => 500,
                'message' => 'Error fetching data',
            ];
            echo json_encode($res);
            return false;
        }
    }
} else {
    // Handle non-POST requests if needed
    echo 'Invalid request method';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inputValue'])) {
    $inputValue = '%' . $_POST['inputValue'] . '%';

    $fetchedData = [];

    // Process the selected data as needed
    // You can perform database queries or other actions based on the selected data

    // Fetch data based on fclt_name
    if (!empty($inputValue)) {
        // Construct the SQL query
        $query = "SELECT referral_forms.*, referral_records.*, facilities.*
                  FROM referral_forms
                  INNER JOIN referral_records ON referral_forms.id = referral_records.rfrrl_id
                  INNER JOIN facilities ON facilities.fclt_id = referral_records.referred_hospital
                  WHERE referral_forms.name LIKE '$inputValue'";
        $query_run = mysqli_query($conn, $query);
    }

    if ($query_run) {
        // Fetch data and store it in the $fetchedData array
        while ($row = mysqli_fetch_assoc($query_run)) {
            $fetchedData[] = $row;
        }

        if (count($fetchedData) > 0) {
            $res = [
                'status' => 200,
                'message' => 'Data fetched',
                'data' => $fetchedData
            ];
        } else {
            $res = [
                'status' => 404,
                'message' => 'No data found',
                'data' => []  // Provide an empty array for consistency
            ];
        }

        // Set Content-Type header to JSON
        header('Content-Type: application/json');

        // Output JSON response
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'Error fetching data',
            'data' => []  // Provide an empty array for consistency
        ];
        // Set Content-Type header to JSON
        header('Content-Type: application/json');

        // Output JSON response
        echo json_encode($res);
        return false;
    }

} else {
    // Handle non-POST requests if needed
    $res = [
        'status' => 400,
        'message' => 'Invalid request method',
        'data' => []  // Provide an empty array for consistency
    ];
    // Set Content-Type header to JSON
    header('Content-Type: application/json');

    // Output JSON response
    echo json_encode($res);
    return false;
}