$(document).ready(function() {
    // Fetch events from the server using AJAX
    $.ajax({
        url: 'getdata.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Initialize evoCalendar and populate it with the fetched events
            $('#calendar').evoCalendar({
                calendarEvents: data
            });
        },
        error: function(error) {
            console.error('Error fetching events:', error);
        }
    });
});
