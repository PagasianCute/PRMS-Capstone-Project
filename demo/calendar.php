<?php

// Database connection
$hostname = "localhost";
$username = "root";
$password = "";
$database = "referraldb";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to get all titles for a specific date
function getAllTitlesForDate($date, $conn)
{
    $sql = "SELECT title FROM events WHERE date = '$date'";
    $result = mysqli_query($conn, $sql);

    $titles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $titles[] = $row['title'];
    }

    return $titles;
}

// Function to generate the dynamic calendar
// Function to generate the dynamic calendar
function generateCalendar($month, $year, $conn)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDay = mktime(0, 0, 0, $month, 1, $year);
    $startDayOfWeek = date('N', $firstDay);

    echo "<h2>" . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . "</h2>"; // Display month and year
    echo "<table style='width: 100%;'>";
    echo "<tr><th style='width: 14.2857%'>Mon</th><th style='width: 14.2857%'>Tue</th><th style='width: 14.2857%'>Wed</th><th style='width: 14.2857%'>Thu</th><th style='width: 14.2857%'>Fri</th><th style='width: 14.2857%'>Sat</th><th style='width: 14.2857%'>Sun</th></tr>";
    echo "<tr>";

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDate = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
        $dayNumber = date('j', strtotime($currentDate)); // Extract the day number

        $titlesCount = count(getAllTitlesForDate($currentDate, $conn));

        echo "<td style='width: 14.2857%;'>";
        echo "<strong>$dayNumber</strong>";

        if ($titlesCount > 0) {
            echo "<br>Titles Count: $titlesCount";
        }

        echo "</td>";

        if (($day + $startDayOfWeek - 1) % 7 == 0) {
            echo "</tr><tr>";
        }
    }

    echo "</tr>";
    echo "</table>";
}


// Get the current month and year
$currentMonth = date('n');
$currentYear = date('Y');

// Display the dynamic calendar
generateCalendar($currentMonth, $currentYear, $conn);

// Close the database connection
mysqli_close($conn);

?>
