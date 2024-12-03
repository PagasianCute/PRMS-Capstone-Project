function loadPatients() {
    // Make an AJAX GET request using jQuery
    $.ajax({
        url: 'server/api.php', // Update the URL to your server-side script
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // Handle the successful response
            displayPatients(data);
        },
        error: function(error) {
            // Handle errors
            console.error('Error:', error);
        }
    });
}

function displayPatients(data) {
    // Clear the existing list
    $('#patientList').empty();

    // Append each patient to the list
    $.each(data, function(index, patient) {
        $('#patientList').append('<li>' + patient.fname + '</li>');
    });
}


$(document).ready(function(){
    $("#alertButton").click(function(){
      // Trigger an alert when the button is clicked
      alert("Hello, this is a jQuery alert!");
    });
  });