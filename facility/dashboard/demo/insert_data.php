<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Include the database connection file
  include 'db_conn.php';

  // Prepare and execute the SQL statement to insert data into the 'myform' table
  $sql = "INSERT INTO myform (label) VALUES (?)";
  $stmt = $conn->prepare($sql);

  foreach ($_POST['labels'] as $label) {
    $stmt->bind_param("s", $label);
    $stmt->execute();
  }

  // Close the statement and database connection
  $stmt->close();
  $conn->close();

  echo "Data inserted successfully!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Include the database connection file
  include 'db_conn.php';

  // Prepare and execute the SQL statement to add a new column 'Email' to the 'mycolumn' table
  $sql = "ALTER TABLE mycolumn ADD $label varchar(255);";
  if ($conn->query($sql)) {
    echo "Column added successfully!";
  } else {
    echo "Error adding column: " . $conn->error;
  }

  // Close the database connection
  $conn->close();
}

