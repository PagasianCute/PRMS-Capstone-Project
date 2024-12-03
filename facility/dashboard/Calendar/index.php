<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="sitoy.css" />
    <link rel="stylesheet" href="evo-calendar.min.css" />
    <link rel="stylesheet" href="evo-calendar.midnight-blue.min.css" />
    <!-- Add the evo-calendar.css for styling -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/evo-calendar@1.1.2/evo-calendar/css/evo-calendar.min.css"/>
</head>
<body>

<div class="hero">
    
    <div id="calendar"></div>
 

</div>  

<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<script src="evo-calendar.min.js"></script>

<script>
$(document).ready(function() {
    $.ajax({
        url: 'getdata.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#calendar').evoCalendar({
                calendarEvents: data
            });
        },
        error: function(error) {
    console.error('Error fetching events:', error);

    // Log more details if available
    if (error.responseText) {
        console.error('Response Text:', error.responseText);
    }
    if (error.status) {
        console.error('Status:', error.status);
    }
    if (error.statusText) {
        console.error('Status Text:', error.statusText);
    }
}
    });
});
</script>

</body>
</html>