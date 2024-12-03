</div>
</div>
<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Boxicons -->
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

<!-- Include Pusher JavaScript -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<!-- Include Bootstrap-datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



<script>

/// JavaScript to toggle the sidebar
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');
const toggleButton = document.getElementById('toggleButton');
let tooltips = [];

toggleButton.addEventListener('click', () => {
  // Check if the sidebar has the "active" class and toggle it
  if (sidebar.classList.contains("active")) {
    sidebar.classList.remove("active");
    sidebar.classList.add("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '80px';

    // Reinitialize tooltips when sidebar becomes inactive
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
  } else {
    sidebar.classList.add("active");
    sidebar.classList.remove("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '250px';
    
    // Dispose tooltips when sidebar becomes active
    tooltips.forEach(tooltip => tooltip.dispose());
    tooltips = []; // Clear the tooltips array
  }
});

// Check the screen width and auto-collapse the sidebar
function checkScreenWidth() {
  if (window.innerWidth <= 1400) {
    sidebar.classList.remove("active");
    sidebar.classList.add("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '80px';

    // Reinitialize tooltips when sidebar becomes inactive
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  } else {
    sidebar.classList.add("active");
    sidebar.classList.remove("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '250px';
    
    // Dispose tooltips when sidebar becomes active
    tooltips.forEach(tooltip => tooltip.dispose());
    tooltips = []; // Clear the tooltips array
  }
}

// Call the function on page load and window resize
window.addEventListener('load', checkScreenWidth);
window.addEventListener('resize', checkScreenWidth);


var pusher = new Pusher('4c140a667948d3f0c3b4', {
    cluster: 'ap1'
});

var channel = pusher.subscribe('my-channel');
channel.bind('my-event', function (data) {

});

</script>

<script>
// Define displaySelectedImage outside the document ready block
function displaySelectedImage() {
    var input = $("#formFile")[0];
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $("#imagePreview").attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    // Function to handle form submission
    $("#user_profile").on("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission behavior

    var formData = new FormData(this);

    // Append the file to the FormData object if it exists
    var fileInput = $("#formFile")[0];
    if (fileInput.files.length > 0) {
        formData.append("profile_image", fileInput.files[0]);
    }

    $.ajax({
        type: "POST",
        url: "upload.php", // Replace with the actual URL to handle the form submission
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
    // Handle the response from the server
          console.log(response);

          try {
              var jsonResponse = JSON.parse(response);

              // Check if the response contains the success message
              if (jsonResponse.message && jsonResponse.message.includes("Staff profile updated successfully!")) {
                  showToast("Profile", "Profile updated successfully!");

                  // Delay the reload to ensure the toast is shown
                  setTimeout(function() {
                      window.location.reload();
                  }, 1000); // You can adjust the delay as needed

                  // Close the modal
                  $("#staffEditModal").modal("hide");
              } else if (jsonResponse.message && jsonResponse.message.includes("Current password is incorrect.")) {
                  // Display an alert for incorrect current password
                  alert("Current password is incorrect.");
              } else {
                  // Handle other response scenarios as needed
                  console.error("Unexpected response:", jsonResponse);
              }
          } catch (error) {
              // Handle the case where the response is not a valid JSON
              console.error("Error parsing JSON:", error);

              // Close the modal even if there is an error (adjust as needed)
              $("#staffEditModal").modal("hide");
          }
      },

        error: function(xhr, status, error) {
            // Handle errors here
            console.error(xhr.responseText);
        }
    });
});




    // Function to handle image upload and preview
    $("#uploadButton").on("click", function(event) {
        event.preventDefault();
        $("#formFile").click();
    });

    $("#formFile").on("change", function(event) {
        // Now we can call the globally defined function
        event.preventDefault();
        displaySelectedImage();
    });

    $("#editButton").click(function() {
        $("#saveButton").removeClass("d-none");
        $("#editButton").addClass("d-none");
        $("#cancelButton").removeClass("d-none");
        $("#user_profile input").removeAttr("readonly");
        $("#uploadButton").prop("disabled", false);
    });
    $("#cancelButton").click(function() {
        $("#saveButton").addClass("d-none");
        $("#editButton").removeClass("d-none");
        $("#cancelButton").addClass("d-none");
        $("#user_profile")[0].reset();
        $("#user_profile input").prop("readonly", true);
        $("#uploadButton").prop("disabled", true);
    });
});
</script>


</body>
</html>