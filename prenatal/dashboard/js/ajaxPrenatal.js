document.addEventListener("DOMContentLoaded", function () {
  const recordsList = document.querySelector(".records .records-list");

  tooltip();

  function playNotificationSound() {
    // Assuming you have an audio file named 'notification.mp3'
    var audio = new Audio("../../assets/notification1.mp3");
    audio.play();
  }

  function tooltip() {
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  $(document).on("click", ".viewPatient", function () {
    var patient_id = $(this).val();
    var $tooltipTrigger = $(this);
    var tooltipInstance = bootstrap.Tooltip.getInstance($tooltipTrigger[0]);

    if (tooltipInstance) {
      tooltipInstance.hide();
    }
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php?view_patient_id=" + patient_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#view_fname").val(res.data.fname);
          $("#view_mname").val(res.data.mname);
          $("#view_lname").val(res.data.lname);
          $("#view_contactNum").val(res.data.contact);
          $("#view_address").val(res.data.address);
          var newHrefValue = "view_patient.php?id=" + res.data.id;
          $("#view-profile").attr("href", newHrefValue);
          $("#viewPatientModal").modal("show");
        }
      },
    });

    let xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "server/prenatal_function.php?get_patient_records=" + patient_id,
      true
    );
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          recordsList.innerHTML = data;
        }
      }
    };
    xhr.send();
  });

  $(document).on("click", ".viewPatientRecords", function () {
    var patients_id = $(this).val();
    $.ajax({
      type: "GET",
      url:
        "server/prenatal_function.php?view_patient_records_id=" + patients_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 404) {
          $("#viewPatientRecordsModal").modal("show");
        } else if (res.status == 200) {
          $("#viewPatientRecordsModal").modal("show");

          $.ajax({
            type: "GET",
            url: "server/prenatal_function.php?patient_count_id=" + patients_id,
            success: function (response) {
              var res = jQuery.parseJSON(response);
              if (res.status == 404) {
                $("#viewPatientRecordsModal .modal-body").empty();
                var alertHtml =
                  '<div class="alert alert-primary d-flex records_alert" role="alert">' +
                  "<h6>No Records</h6>" +
                  "</div>";

                // Append the alert to the modal body
                $("#viewPatientRecordsModal .modal-body").append(alertHtml);
              } else if (res.status == 200) {
                $("#viewPatientRecordsModal .modal-body").empty();

                if (Array.isArray(res.data) && res.data.length > 0) {
                  // Loop through the data and append the alert for each item
                  res.data.forEach(function (item) {
                    var alertHtml =
                      '<div class="alert alert-primary d-flex records_alert" role="alert">' +
                      "<h6>Record " +
                      item.records_count +
                      "</h6>" +
                      '<a class="btn btn-primary" href="view_prenatal.php?id=' +
                      item.patients_id +
                      "&record=" +
                      item.records_count +
                      '" role="button">View</a>' +
                      "</div>";

                    // Append the alert to the modal body
                    $("#viewPatientRecordsModal .modal-body").append(alertHtml);
                  });
                }

                // Show the modal
                $("#viewPatientRecordsModal").modal("show");
              }
            },
          });
        }
      },
    });
  });

  $(document).on("click", ".createNewPrenatalRecord", function () {
    var patients_id = $(this).val();

    $("#recordConfirmation").modal("show");

    $(document).on("click", ".recordConfirmed", function () {
      $.ajax({
        type: "POST",
        url: "server/prenatal_function.php",
        data: {
          new_record: true,
          patients_id: patients_id,
        },
        dataType: "json", // Specify the expected data type
        success: function (res) {
          console.log(res);
          if (res.status == 200) {
            var nextPageUrl = "view_prenatal.php?id=" + patients_id;
            window.location.href = nextPageUrl;
          } else if (res.status == 300) {
            $("#errorMessage").removeClass("d-none");
            $("#errorMessage").text(res.message);
          }
        },
        error: function (xhr, status, error) {
          // Handle error situations
          console.error(xhr.responseText);
        },
      });
    });
  });

  $(document).on("submit", "#addPatient", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append("add_patient", true);

    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
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
          $("#errorMessage").addClass("d-none");
          $("#staticBackdrop").modal("hide");
          $("#addPatient")[0].reset();
          playNotificationSound();
          setTimeout(function () {
            showToast("Patient Registration", "Patient Added");
          }, 1000);

          $("#nav_buttons").load(location.href + " #nav_buttons");
          $("#table").load(location.href + " #table");
        }
      },
    });
  });

  $(document).on("click", ".deletePatient", function (e) {
    e.preventDefault();

    if (confirm("Are you sure you want to delete this patient?")) {
      var patientID = $(this).val();

      $.ajax({
        type: "POST",
        url: "server/prenatal_function.php",
        data: {
          delete_patient: true,
          patient_id: patientID,
        },
        success: function (response) {
          var res = jQuery.parseJSON(response);
          console.log(res);
          if (res.status == 500) {
            alert(res.message);
          } else {
            playNotificationSound();
            setTimeout(function () {
              showToast("Patient", "Patient Deleted");
            }, 1000);
            $("#table").load(location.href + " #table");
            $("#fieldForm").load(location.href + " #fieldForm");
          }
        },
      });
    }
  });

  $(document).on("click", ".viewAppointment", function (e) {
    e.preventDefault();
    var appointment_id = $(this).val();

    $("#appointment_form input").prop("disabled", true);
    $(".submitBtn").addClass("d-none");
    $(".editBtn").removeClass("d-none");
    $(".proceedBtn").removeClass("d-none");
    $(".cancel").addClass("d-none");

    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php?view_appointment_id=" + appointment_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          console.log(res.data.schedule_id);
          $("#declineAppointment").val(res.data.schedule_id);
          $("#patients_name").val(
            res.data.lname + ", " + res.data.fname + " " + res.data.mname
          );
          $("#appointment_id").val(res.data.schedule_id);
          $("#patients_id").val(res.data.patients_id);
          $("#trimesters").val(res.data.trimester);
          $("#checkup").val(res.data.check_up);
          $("#date_of_return").val(res.data.date);
          $("#record").val(res.data.record);
          $("#appointmentModal").modal("show");
          var currentDate = new Date();
          var formattedDate = currentDate.toISOString().split("T")[0];
          if (formattedDate == res.data.date) {
            $(".proceedBtn").removeClass("disabled");
          } else {
            $(".proceedBtn").addClass("disabled");
          }
          $(".proceedBtn").attr(
            "href",
            "view_prenatal.php?id=" +
              res.data.patients_id +
              "&record=" +
              res.data.record +
              "&trimester=" +
              res.data.trimester +
              "&checkup=" +
              res.data.check_up
          );
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

  $(document).on("submit", "#appointment_form", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append("update_appointment", true);

    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
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
          $("#appointmentModal").modal("hide");
          $("#appoinment_table").load(location.href + " #appoinment_table");
        }
      },
    });
  });

  $(document).on("click", "#declineAppointment", function (e) {
    e.preventDefault();
    var schedule_id = $(this).val();

    if (confirm("Are you sure you want to decline this appointment?")) {
      $.ajax({
        type: "POST",
        url: "server/prenatal_function.php",
        data: {
          update_schedule: true,
          schedule_id: schedule_id,
        },
        success: function (response) {
          var res = jQuery.parseJSON(response);
          if (res.status == 500) {
            alert(res.message);
          } else {
            alert(res.message);
            $("#appointmentModal").modal("hide");
            $("#appoinment_table").load(location.href + " #appoinment_table");
            playNotificationSound();
            setTimeout(function () {
              showToast("Appoinment", "Appointment Declined");
            }, 1000);
          }
        },
      });
    }
  });
});
