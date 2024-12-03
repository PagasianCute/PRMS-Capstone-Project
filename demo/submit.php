<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve selected values from the form
    $region = $_POST["region"];
    $region_name = $_POST["region-name"];
    $province = $_POST["province"];
    $municipality = $_POST["municipality"];
    $barangay = $_POST["barangay"];

    // Now you can insert these values into your database table using your preferred database connection method
    // For example, using PDO:
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=referraldb", "root", "");

        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO your_table (region, province, municipality, barangay) VALUES (?, ?, ?, ?)");

        // Execute the statement with the selected values
        $stmt->execute([$region_name, $province, $municipality, $barangay]);

        // Optionally, you can redirect the user to a success page
        header("Location: index.html?success");
        exit();
    } catch (PDOException $e) {
        // Handle database connection or query errors
        echo "Error: " . $e->getMessage();
    }
}
?>
