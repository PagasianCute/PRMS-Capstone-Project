document.addEventListener("DOMContentLoaded", () => {
  $(document).on("click", ".viewRecord", function () {
    var rffrl_id = $(this).val();
    $('#arrival_form input').val('');
    $.ajax({
      type: "GET",
      url: "server/new_function.php?rffrl_id=" + rffrl_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#fclt_name").text(res.data.fclt_name);
          $("#rffrl_id").val(res.data.rfrrl_id);
          $("#name").val(res.data.name);
          $("#age").val(res.data.age);
          $("#sex").val(res.data.sex);
          $("#bdate").val(res.data.bdate);
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
          $("#arrival").val(res.data.arrival);
          $("#expected_time").val(updateTimeDisplay(res.data.expected_arrival));
          $("#patient_status_upon_arrival").val(
            res.data.patient_status_upon_arrival
          );
          if (res.data.lname || res.data.fname || res.data.mname) {
            $("#receiving_officer").val(
              res.data.lname + ", " + res.data.fname + " " + res.data.mname
            );
          } else {
            $("#receiving_officer").val("");
          }
          $("#arrival_date").val(formatDate(res.data.arrival_date));
          $("#arrival_time").val(updateTimeDisplay(res.data.arrival_time));
          $("#referralModal").modal("show");

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

  function updateTimeDisplay(arrivalTime) {
    // Split the time into hours and minutes
    var timeParts = arrivalTime.split(":");
    var hours = parseInt(timeParts[0], 10);
    var minutes = parseInt(timeParts[1], 10);
  
    // Create a Date object and set the hours and minutes
    var formattedTime = new Date();
    formattedTime.setHours(hours);
    formattedTime.setMinutes(minutes);
  
    // Get the formatted time with AM/PM
    var formattedTimeString = formattedTime.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
  
    // Assuming you want to display it in an element with ID "formatted_time"
    return formattedTimeString;
  }

  $(document).on("click", "#restore_button", function () {
    var formData = new FormData($("#referral_form")[0]);
    formData.append("restore_referral", true);

    $.ajax({
        type: "POST",
        url: "server/new_function.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            var res = jQuery.parseJSON(response);
            if (res.status == 200) {
                $("#referralModal").modal("hide");
                $("#yourDivId").load(location.href + " #yourDivId");
            }
        },
    });
});
});