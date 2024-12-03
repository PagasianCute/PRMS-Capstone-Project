document.addEventListener("DOMContentLoaded", function () {
  var channel = pusher.subscribe(fclt_id);
  var user_channel = pusher.subscribe(user_id);

  channel.bind("referral", function (data) {
    console.log("Received referral from: " + data);
    $(".newReferrals").load(location.href + " .newReferrals");
  });

  function reloadContent() {
    $.ajax({
      url: location.href + "?timestamp=" + new Date().getTime(),
      type: "GET",
      success: function (response) {
        var newPatientArrivalContent = $(response)
          .find(".newPatientArrival")
          .html();
        $(".newPatientArrival").html(newPatientArrivalContent);
      },
      error: function (error) {
        console.error("Error reloading content: ", error);
      },
    });
  }

  // Event handler for "doctor_referral_accept" event
  user_channel.bind("doctor_referral_accept", function (data) {
    console.log("Received referral from: " + data);

    // Reload the content of the div
    reloadContent();
  });

  const rightBtn = document.querySelectorAll(".nav-link.right-button");
  const leftBtn = document.querySelectorAll(".nav-link.left-button");
  var selectElement = document.getElementById("referralRecordsCount");
  var patientID;

  $(document).on("click", ".viewRecord", function () {
    var rffrl_id = $(this).val();

    $.ajax({
      type: "GET",
      url: "server/new_referrals_function.php?rffrl_id=" + rffrl_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#fclt_name").text(res.data.fclt_name);
          $("#conducting_person").text(
            res.data.lname + ", " + res.data.fname + " " + res.data.mname
          );
          $("#conducting_person_img").prop(
            "src",
            "../../assets/" + res.data.img
          );
          $("#fclt_id").val(res.data.fclt_id);
          $("#rffrl_id").val(res.data.rfrrl_id);
          $("#name").val(res.data.name);
          $("#age").val(res.data.age);
          $("#sex").val(res.data.sex);
          $("#bdate").val(formatDate(res.data.bdate));
          $("#address").val(res.data.address);
          $("#admitting_dx").val(res.data.admitting_dx);
          $("#rtpcr").val(res.data.rtpcr);
          $("#antigen").val(res.data.antigen);
          $("#clinical_ssx").val(res.data.clinical_ssx);
          $("#exposure_to_covid").val(res.data.exposure_to_covid);
          $("#temp").val(res.data.temp);
          $("#hr").val(res.data.hr);
          $("#resp").val(res.data.resp);
          $("#bp").val(res.data.bp);
          $("#02sat").val(res.data.O2sat);
          $("#02aided").val(res.data.O2aided);
          $("#procedures_need").val(res.data.procedures_need);
          $("#fh").val(res.data.fh);
          $("#ie").val(res.data.ie);
          $("#fht").val(res.data.fht);
          $("#lmp").val(res.data.lmp);
          $("#edc").val(res.data.edc);
          $("#aog").val(res.data.aog);
          $("#utz").val(res.data.utz);
          $("#utz_aog").val(res.data.utz_aog);
          $("#edd").val(res.data.edd);
          $("#enterpretation").val(res.data.enterpretation);
          $("#diagnostic_test").val(res.data.diagnostic_test);
          $("#view_referral_reason").val(res.data.referral_reason);
          $(".referral-reason").addClass("d-none");
          $("#cancel_button").addClass("d-none");
          $("#decline_button").addClass("d-none");
          $("#referralModal").modal("show");
          $("#for_contact").val(res.data.rfrrl_id);
          patientID = res.data.patients_id;
          console.log(patientID);

          // Display referral transactions
          var querytransactions_data = res.transactions;
          var referralTransactionsDiv = $("#referral_transactions");
          var audit = document.querySelector(".referral-audit");
          referralTransactionsDiv.empty(); // Clear any previous data

          for (var i = 0; i < querytransactions_data.length; i++) {
            var transactionData = querytransactions_data[i];
            var status = transactionData.status;
            var time = transactionData.time;
            var fclt_name = transactionData.fclt_name;
            var reasonForReferral = transactionData.reason; // Assuming you have a property named reasonForReferral

            if (status) {
              audit.classList.remove("d-none");
              var pElement = $("<p></p>"); // Create a new <p> element
              pElement.text(status + " by " + fclt_name + " at " + time); // Include the label
              referralTransactionsDiv.append(pElement); // Append the <p> element to the div

              // Check if there is a reason for referral that is not "NULL" and add another <p> tag
              if (reasonForReferral && reasonForReferral !== "NULL") {
                var reasonForReferralElement = $("<p></p>");
                reasonForReferralElement.text(
                  "Reason for Referral: " + reasonForReferral
                );
                referralTransactionsDiv.append(reasonForReferralElement);
              }
            }
          }

          const referralModal = document.getElementById("referralModal");
          referralModal.addEventListener("hidden.bs.modal", (event) => {
            $(".referral-reason").addClass("d-none");
            $("#cancel_button").addClass("d-none");
            $("#decline_button").addClass("d-none");
            $("#decline_referral").removeClass("d-none");
            $("#accept_button").removeClass("d-none");
          });
        }
      },
    });
  });

  function formatDate(inputDate) {
    // Parse the input date
    const parsedDate = new Date(inputDate);

    // Check if the parsed date is not a valid date
    if (isNaN(parsedDate)) {
      return "";
    }

    // Months array to convert numerical month to string
    const months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ];

    // Get month, day, and year
    const month = months[parsedDate.getMonth()];
    const day = parsedDate.getDate();
    const year = parsedDate.getFullYear();

    // Suffix for day (st, nd, rd, or th)
    let daySuffix;
    if (day >= 11 && day <= 13) {
      daySuffix = "th";
    } else {
      switch (day % 10) {
        case 1:
          daySuffix = "st";
          break;
        case 2:
          daySuffix = "nd";
          break;
        case 3:
          daySuffix = "rd";
          break;
        default:
          daySuffix = "th";
      }
    }

    // Format the date
    const formattedDate = `${month} ${day}${daySuffix}, ${year}`;
    return formattedDate;
  }

  $("#createReferralModal").on("hidden.bs.modal", function () {
    // Set the "Referral Record" button as active
    $("#referral_create-tab").addClass("active");
    // Remove the "active" class from other buttons
    $(
      "#patientInformation_create-tab, #birthExperience_create-tab, #trimesters_create-tab"
    ).removeClass("active");

    // Display the content of the "Referral Record" tab
    $("#referral_create").addClass("show active");
    // Hide the content of other tabs
    $(
      "#patientInformation_create, #birthExperience_create, #trimesters_create"
    ).removeClass("show active");

    // Optionally, reset the value of the select element
    $("#referralRecordsCount_referralCreate").val("");
  });

  $(document).on("click", ".arrivalRecord", function () {
    var rffrl_id = $(this).val();
    $.ajax({
      type: "GET",
      url: "server/new_referrals_function.php?rffrl_id=" + rffrl_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#fclt_name").text(res.data.fclt_name);
          $("#fclt_id").val(res.data.fclt_id);
          $("#rffrl_id").val(res.data.rfrrl_id);
          $("#name").val(res.data.name);
          $("#age").val(res.data.age);
          $("#sex").val(res.data.sex);
          $("#bdate").val(formatDate(res.data.bdate));
          $("#address").val(res.data.address);
          $("#admitting_dx").val(res.data.admitting_dx);
          $("#rtpcr").val(res.data.rtpcr);
          $("#antigen").val(res.data.antigen);
          $("#clinical_ssx").val(res.data.clinical_ssx);
          $("#exposure_to_covid").val(res.data.exposure_to_covid);
          $("#temp").val(res.data.temp);
          $("#hr").val(res.data.hr);
          $("#resp").val(res.data.resp);
          $("#bp").val(res.data.bp);
          $("#02sat").val(res.data.O2sat);
          $("#02aided").val(res.data.O2aided);
          $("#procedures_need").val(res.data.procedures_need);
          $("#fh").val(res.data.fh);
          $("#ie").val(res.data.ie);
          $("#fht").val(res.data.fht);
          $("#lmp").val(res.data.lmp);
          $("#edc").val(res.data.edc);
          $("#aog").val(res.data.aog);
          $("#utz").val(res.data.utz);
          $("#utz_aog").val(res.data.utz_aog);
          $("#edd").val(res.data.edd);
          $("#enterpretation").val(res.data.enterpretation);
          $("#diagnostic_test").val(res.data.diagnostic_test);
          $("#view_referral_reason").val(res.data.referral_reason);
          $(".referral-reason").addClass("d-none");
          $("#cancel_button").addClass("d-none");
          $("#decline_button").addClass("d-none");
          $("#referralModal").modal("show");
          $("#for_contact").val(res.data.rfrrl_id);
          patientID = res.data.patients_id;
          console.log(patientID);
        }
      },
    });
    $("#viewBtnClose").removeClass("d-none");
    $("#accept_button").addClass("d-none");
    $("#decline_referral").addClass("d-none");
    $("#for_contact").addClass("d-none");
  });

  $(document).on("click", ".patientArrived", function () {
    var rffrl_id = $(this).val();
    $("#arrivalModal").modal("show");
    // Get current timestamp
    var currentTimestamp = $.now();
    // Set the formatted date and time to #arrival_date and #arrival_time
    var date = $.format.date(currentTimestamp, "yyyy-MM-dd");
    var time = $.format.date(currentTimestamp, "HH:mm");

    $("#arrival_date").val(date);
    $("#arrival_time").val(time);

    $(document).on("submit", "#arrival_form", function (e) {
      e.preventDefault();
      var formData = new FormData(this);
      formData.append("patient_arrived", true);
      formData.append("rffrl_id", rffrl_id);
      $.ajax({
        type: "POST",
        url: "server/new_function.php",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
          $(".arrival-loading").removeClass("d-none");
          $(".patientArrivedSubmit").addClass("disabled");
          $(".arrival-span").addClass("d-none");
        },
        success: function (response) {
          var res = jQuery.parseJSON(response);
          if (res.status == 200) {
            $("#arrivalModal").modal("hide");
            $("#arrival_form")[0].reset();
            $(".newPatientArrival").load(location.href + " .newPatientArrival");
            playNotificationSound();
            setTimeout(function () {
              showToast("Patient Arrival", "Arrival sent.");
            }, 1000);
          } else if (res.status == 500) {
            alert(res.message);
          }
        },
        complete: function () {
          $(".arrival-loading").addClass("d-none");
          $(".patientArrivedSubmit").removeClass("disabled");
          $(".arrival-span").removeClass("d-none");
        },
      });
    });
  });

  function playNotificationSound() {
    // Assuming you have an audio file named 'notification.mp3'
    var audio = new Audio("../../assets/notification1.mp3");
    audio.play();
  }

  $(document).on("click", ".sendToDoctor", function () {
    var rfrrl_id = $(this).val();
    $("#send_to_doctor_modal").modal("show");

    $(document).off("submit", "#send_to_doctor_form");

    $(document).on("submit", "#send_to_doctor_form", function (e) {
      e.preventDefault();

      var formData = new FormData(this);
      formData.append("submit_referral", true);
      formData.append("rfrrl_id", rfrrl_id);

      $.ajax({
        type: "POST",
        url: "server/api.php",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
          $(".arrival-loading").removeClass("d-none");
          $(".patientArrivedSubmit").addClass("disabled");
          $(".arrival-span").addClass("d-none");
        },
        success: function (response) {
          console.log(response);
          var res = jQuery.parseJSON(response);
          if (res.status == 422) {
            console.log(res); // Log the response for debugging
            $("#errorMessage").removeClass("d-none");
            $("#errorMessage").text(res.message);
          } else if (res.status == 200) {
            $("#send_to_doctor_modal").modal("hide");
            $(".newReferrals").load(location.href + " .newReferrals");
            reloadNotifications();
            playNotificationSound();
            setTimeout(function () {
              showToast("Referral Submit", "Referral submitted successfully");
            }, 1000);
          }
        },
        complete: function () {
          $(".arrival-loading").addClass("d-none");
          $(".patientArrivedSubmit").removeClass("disabled");
          $(".arrival-span").removeClass("d-none");
        },
      });
    });
  });

  $(document).on("submit", "#update_to_doctor_form", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append("edit_submit_referral", true);

    $.ajax({
      type: "POST",
      url: "server/api.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log(response);
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          console.log(res); // Log the response for debugging
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        } else if (res.status == 200) {
          $("#edit_send_to_doctor_modal").modal("hide");
          $(".newReferrals").load(location.href + " .newReferrals");
          loadNotifications();
          playNotificationSound();
          setTimeout(function () {
            showToast("Referral Submit", "Referral Edited");
          }, 1000);
        }
      },
    });
  });

  $(document).on("click", "#referrals-btn", function () {
    var staff_id = $(this).val();

    $.ajax({
      type: "GET",
      url: "server/new_function.php?doctors_referral_id=" + staff_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          // Clear previous table content
          $("#all_sended_referrals .modal-body").empty();

          // Create a table element
          var table = $('<table class="table" id="sent-referrals-table">');

          // Add table headers
          table.append(
            "<thead><tr><th>Referral ID</th><th>Doctor Name</th><th>Status</th><th>Action</th></tr></thead>"
          );

          // Create table body and populate rows
          var tbody = $("<tbody>");

          // Check if there is data in the response
          if (res.data.length > 0) {
            $.each(res.data, function (index, row) {
              var tr = $("<tr>");
              tr.append("<td>" + row.rfrrl_id + "</td>");
              tr.append(
                "<td>" +
                  row.lname +
                  ", " +
                  row.fname +
                  " " +
                  row.mname +
                  "</td>"
              );
              tr.append(
                "<td>" + formattedDate(row.sent_date, row.sent_time) + "</td>"
              );
              tr.append(
                '<td class="sent-edit-btn"><button id="icon-btn" type="button" value="' +
                  row.id +
                  '" class="editSendDoctor"><i class="fi fi-rr-edit"></i></button></td>'
              );
              tbody.append(tr);
            });
          } else {
            // If no data found, display a row with the message
            var noDataRow = $('<tr><td colspan="4">No data found</td></tr>');
            tbody.append(noDataRow);
          }

          // Append table body to the table
          table.append(tbody);

          // Append the table to the modal body
          $("#all_sended_referrals .modal-body").append(table);

          // Show the modal
          $("#all_sended_referrals").modal("show");
        }
      },
    });
  });

  $(document).on("click", ".editSendDoctor", function () {
    var doctors_referral_id = $(this).val();

    $.ajax({
      type: "GET",
      url:
        "server/new_function.php?get_doctors_referral_id=" +
        doctors_referral_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#doctor-referral-span").text(res.data.rfrrl_id);
          $("#doctors_referral_id").val(res.data.id);
          $("#all_sended_referrals").modal("hide");
          $("#edit_send_to_doctor_modal").modal("show");
        }
      },
    });
  });

  function formattedDate(sentDate, sentTime) {
    // Combine the date and time strings
    var rowDateTimeString = sentDate + " " + sentTime;

    // Convert the combined string to a JavaScript Date object
    var rowDateTime = new Date(rowDateTimeString);

    // Get the current time
    var currentDateTime = new Date();

    // Calculate the time difference in milliseconds
    var timeDifference = currentDateTime - rowDateTime;

    // Convert the time difference to minutes
    var minutesDifference = Math.floor(timeDifference / (1000 * 60));

    // Check if the time difference is less than or equal to 2 minutes
    if (minutesDifference <= 2) {
      return "Just now";
    }

    // Format the result
    if (minutesDifference < 60) {
      return minutesDifference + " minutes ago";
    } else {
      var hoursDifference = Math.floor(minutesDifference / 60);
      return hoursDifference + " hours ago";
    }
  }
});
