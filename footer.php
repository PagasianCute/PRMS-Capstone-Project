
<!-- Include Bootstrap CSS -->
<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Boxicons -->
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

<!-- Include Pusher JavaScript -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<!-- Include Bootstrap-datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


<script>


var pusher = new Pusher('4c140a667948d3f0c3b4', {
    cluster: 'ap1'
});
var user_channel = pusher.subscribe(user_id);

var fclt_channel = pusher.subscribe(fclt_id);

function playNotificationSound() {
  // Assuming you have an audio file named 'notification.mp3'
  var audio = new Audio('assets/notification1.mp3');
  audio.play();
}

function normalReferralPlayNotificationSound() {
  // Assuming you have an audio file named 'notification.mp3'
  var audio = new Audio('assets/routine.mp3');
  audio.play();
}

function highReferralPlayNotificationSound() {
  // Assuming you have an audio file named 'notification.mp3'
  var audio = new Audio('assets/urgent.mp3');
  audio.play();
}

  fclt_channel.bind("message", function (data) {
    var fclt_id = data.fclt_id;
    var fclt_name = data.fclt_name;

    message = "New Message from: " + fclt_name;
    reloadNotifications();
    MessageshowToast("Message", message, fclt_id);
    playNotificationSound();
  });

  fclt_channel.bind("referral", function (data) {
  var fclt_name = data.fclt_name;
  var emergency_type = data.emergency_type;
  console.log(emergency_type);

  reloadNotifications();
  message = "New Referral From: " + fclt_name;
  if (emergency_type === 'Routine') {
    newReferralshowToast("Referral", message, 'assets/routine.mp3');
  } else {
    newReferralshowToast("Referral", message, 'assets/urgent.mp3');
  }
  $("#referralDiv").load(location.href + " #referralDiv > *");
});

  function newReferralshowToast(title, message, audioSource) {
  var toastId = "toast" + new Date().getTime();
  var toastElement = document.createElement("div");
  toastElement.id = toastId;
  toastElement.className = "toast";
  toastElement.role = "alert";
  toastElement.setAttribute("aria-live", "assertive");
  toastElement.setAttribute("aria-atomic", "true");

  var audio = new Audio(audioSource);

  toastElement.innerHTML = `
    <div class="toast-header">
      <strong class="me-auto">${title}</strong>
    </div>
    <div class="toast-body">
      ${message}
      <div class="mt-2 pt-2 border-top">
        <a class="btn btn-primary btn-sm referralView" href="new_referrals.php" role="button">View</a>
        <button type="button" class="btn btn-secondary btn-sm close-toast">Close</button>
      </div>
    </div>
  `;

  document.querySelector(".toast-container").appendChild(toastElement);

  var newToast = new bootstrap.Toast(document.getElementById(toastId), { autohide: false });

  $("#" + toastId + " .close-toast").on("click", function () {
    newToast.hide();
  });

  $("#" + toastId).on("hidden.bs.toast", function () {
    // Check if the audio is playing before attempting to stop it
    if (!audio.paused) {
      audio.pause();
      audio.currentTime = 0;
    }
  });

  newToast.show();

  // Check if the audio is not already playing before calling play()
  if (audio.paused) {
    audio.play();
  }
}

  fclt_channel.bind("referral_accept", function (data) {
    reloadNotifications();
    message = "Your Referral is Accepted by: " + data;
    showToast("Referral Transaction", message);
    playNotificationSound();
    $("#referralDiv").load(location.href + " #referralDiv > *");
  });

  fclt_channel.bind("patient_arrival", function (data) {
    reloadNotifications();
    message = "Your Patient arrived at: " + data;
    showToast("Patient Arrival", message);
    playNotificationSound();
  });

  fclt_channel.bind("referral_declined", function (data) {
    reloadNotifications();
    var reason = data.reason;
    var fclt_name = data.fclt_name;
    message = "Your Referral is Declined by " + fclt_name + "for a reason: " + reason;
    showToast("Referral Transaction", message);
    playNotificationSound();
    showToast("Transfering Referral", "Your referral will be transfer to Caraga Hospital");
    $("#referralDiv").load(location.href + " #referralDiv > *");
  });

  fclt_channel.bind("referral_process", function (data) {
    reloadNotifications();
    message = "Your Referral is on process by: " + data;
    showToast("Referral Process", message);
    playNotificationSound();
    $("#referralDiv").load(location.href + " #referralDiv > *");
  });

  user_channel.bind("doctor_referral_accept", function (data) {
    reloadNotifications();
    showToast("Dr. " + data, "Referral Accepted, please wait for arrival.");
    playNotificationSound();
  });

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
    $("#user_profile").on("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    var formData = new FormData(this);

    // Append the file to the FormData object if it exists
    var fileInput = $("#formFile")[0];
    if (fileInput.files.length > 0) {
        formData.append("profile_image", fileInput.files[0]);
    }

    $.ajax({
        type: "POST",
        url: "facility/dashboard/upload.php", // Replace with the actual URL to handle the form submission
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            // Handle the response from the server
            console.log(response);

            // Check if the response contains the success message
            if (response.includes("Staff profile updated successfully!")) {
                showToast("Profile", "Profile updated successfully!");

                // Delay the reload to ensure the toast is shown
                setTimeout(function () {
                    window.location.reload();
                }, 1000); // You can adjust the delay as needed
                $("#staffEditModal").modal("hide");
            } else if (response.includes("Current password is incorrect.")) {
                // Password is incorrect, show an alert
                alert("Current password is incorrect.");
            }
        },
        error: function (xhr, status, error) {
            // Handle errors here
            console.error(xhr.responseText);
        }
    });

    // Optional: Close the modal or perform other actions as needed
    // 
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