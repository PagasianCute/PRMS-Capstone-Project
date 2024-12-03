document.addEventListener("DOMContentLoaded", function () {
  function playNotificationSound() {
    // Assuming you have an audio file named 'notification.mp3'
    var audio = new Audio("../../assets/notification1.mp3");
    audio.play();
  }

  $(document).on("submit", "#addRecord", function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === "birthSave") {
      formData.append("add_birth", true);
    } else if (clickedButton.id === "birthUpdate") {
      formData.append("update_birth", true);
    }

    $.ajax({
      type: "POST",
      url: "server/inventory_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          console.log(res); // Log the response for debugging
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        } else if (res.status == 200) {
          $("#staticBackdrop").modal("hide");
          $("#addRecord")[0].reset();
          playNotificationSound();
          setTimeout(function () {
            showToast("Birth Booklet", "Record Saved");
          }, 1000);
          $("#table").load(location.href + " #table");
        }
      },
    });
  });

  $(document).on("click", ".viewRecord", function (e) {
    e.preventDefault();
    var record_id = $(this).val();
    $("#birthUpdate").removeClass("d-none");
    $("#birthSave").addClass("d-none");

    $.ajax({
      type: "GET",
      url: "server/inventory_function.php?view_record_id=" + record_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#case_number").val(res.data.case_number);
          $("#admission_date").val(res.data.admission_date);
          $("#admission_time").val(res.data.admission_time);
          $("#lname").val(res.data.lname);
          $("#fname").val(res.data.fname);
          $("#mname").val(res.data.mname);
          $("#birth_date").val(res.data.birth_date);
          $("#barangay-select").val(res.data.barangay);
          $("#orvida").val(res.data.orvida);
          $("#para").val(res.data.para);
          $("#age_of_gestation").val(res.data.age_of_gestation);
          $("#gender").val(res.data.gender);
          $("#head_circum").val(res.data.head_circum);
          $("#chest_circum").val(res.data.chest_circum);
          $("#length").val(res.data.length);
          $("#weigth").val(res.data.weigth);
          $("#discharge_date").val(res.data.discharge_date);
          $("#discharge_time").val(res.data.discharge_time);
          $("#birth_attendant").val(res.data.birth_attendant);
          $("#staticBackdrop").modal("show");
        }
      },
    });

    $(document).on("click", ".editBtn", function (e) {
      e.preventDefault();
      $(".submitBtn").removeClass("d-none");
      $(".cancel").removeClass("d-none");
      $(".editBtn").addClass("d-none");
      $(".proceedBtn").addClass("d-none");
      $("#appointment_form input").prop("disabled", false);
    });
  });

  $(document).on("click", ".deleteRecord", function (e) {
    e.preventDefault();
    var case_number = $(this).val();

    $.ajax({
      type: "POST",
      url: "server/inventory_function.php",
      data: {
        delete_record: true,
        case_number: case_number,
      },
      success: function (response) {
        console.log(response);
        var res = jQuery.parseJSON(response);
        console.log(res);
        if (res.status == 500) {
          alert(res.message);
        } else {
          playNotificationSound();
          setTimeout(function () {
            showToast("Patient", "Record Deleted");
          }, 1000);
          $("#table").load(location.href + " #table");
          $("#fieldForm").load(location.href + " #fieldForm");
        }
      },
      error: function(xhr, status, error) {
        // Handle the error here
        console.log('AJAX Error: ' + status + ', ' + error);
        
        // You can also display an error message to the user
        alert('An error occurred while processing your request. Please try again later.');
        
        // Optionally log additional details, such as the status code or the response text
        console.log('Response Text: ' + xhr.responseText);
    }
    });
  });

  $(document).on("click", "#buttonCreate", function (e) {
    e.preventDefault();
    $("#addRecord")[0].reset();
    $("#birthUpdate").addClass("d-none");
    $("#birthSave").removeClass("d-none");
    $("#staticBackdrop").modal("show");
  });
});
