<?php
 
// insert_form_values.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    include 'db_conn.php';

    $query = "SELECT label FROM myform";
    $query_run = mysqli_query($conn, $query);

    $dataArray = array();

    if (mysqli_num_rows($query_run) > 0) {
        while ($row = mysqli_fetch_assoc($query_run)) {
            foreach ($row as $field => $value) {
                // Add each value to the $dataArray
                $dataArray[] = $value;
            }
        }
    }

    // Prepare the SQL statement to insert data into the 'mycolumn' table
    $sql = "INSERT INTO mycolumn (" . implode(',', $dataArray) . ") VALUES (". str_repeat('?, ', count($dataArray) - 1) . "?)";
    $stmt = $conn->prepare($sql);

    // Check if the prepared statement is valid
    if ($stmt) {
        // Bind the form data to the prepared statement
        $stmt->bind_param(str_repeat('s', count($dataArray)), ...array_values($_POST));

        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error: Unable to prepare the statement.";
    }

    // Close the database connection
    mysqli_close($conn);
}
 




