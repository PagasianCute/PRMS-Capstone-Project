$(document).ready(function(){
const facilityCheckboxes = document.querySelectorAll('.facility .btn-check');
const statusCheckboxes = document.querySelectorAll('.status .btn-check');

// Initialize objects to store checked labels for each group
const checkedLabels = {
  fclt_name: [],
  status: [],
  // Add more groups if needed
};

function addEventListenerToGroup(group, groupName) {
  group.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      // Update the checked labels for the current group
      checkedLabels[groupName] = Array.from(group)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => `'${document.querySelector(`[for="${checkbox.id}"]`).textContent}'`);

      // Construct the filter data object
      const filterData = {
        fclt_name: checkedLabels.fclt_name,
        status: checkedLabels.status
        // Add more groups if needed
      };

      // Display the checked labels for each group in the alert
      const alerts = Object.keys(checkedLabels)
        .filter(group => checkedLabels[group].length > 0)
        .map(group => `${group}: ${checkedLabels[group].join(', ')}`)
        .join('\n');

      if (alerts.length > 0) {

        sendFilterDataToServer(filterData);
      } else {
        //alert(`No ${groupName.toLowerCase()} are checked.`);
        $('#referralsTable').load(location.href + " #referralsTable");
      }
    });
  });
}


// Add event listeners to each group
addEventListenerToGroup(facilityCheckboxes, 'fclt_name');
addEventListenerToGroup(statusCheckboxes, 'status');

function sendFilterDataToServer(filterData) {
  // Use AJAX to send data to the server
  $.ajax({
    type: 'POST',
    url: 'server/filter_function.php',
    data: filterData,
    success: function (response) {
      // Parse the JSON response
      var data = JSON.parse(response);

      // Check if data is successfully fetched
      if (data.status === 200) {
        // Clear existing table rows
        $('#referralsTable tbody').empty();

        // Loop through the fetched data and append rows to the table
        data.data.forEach(function (row, index) {
          $('#referralsTable tbody').append(`
            <tr>
              <th scope="row">${index + 1}</th>
              <td>${row.fclt_name}</td>
              <td>${row.name}</td>
              <td class="action-column" id="${row.status}-column"><p>${row.status}</p></td>
              <td>${row.date} • ${row.time}</td>
              <td class="action-column">
                <button id="icon-btn" type="button" value="${row.rffrl_id}" class="viewMyRecord">
                  <i class="fi fi-rr-eye"></i>
                </button>
              </td>
            </tr>
          `);
        });
      } else {
        // Handle the case when no data is fetched
        $('#referralsTable tbody').html('<tr><td colspan="6">No records found</td></tr>');
      }
    },
    error: function (error) {
      console.error('Error:', error);
    }
  });
}
});

$(document).ready(function() {
  // Attach the input event handler to the input field
  $('#patients_name').on('input', function() {
    // Get the current value of the input field
    var inputValue = $(this).val();

    // Display the value in the console
    console.log(inputValue);
  });
});