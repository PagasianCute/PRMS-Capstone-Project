$(document).on("submit", "#createReferral", function (e) {
  e.preventDefault();

  $("#submitButton").addClass("d-none");

  var formData = new FormData(this);
  formData.append("create_referral", true);

  $.ajax({
    type: "POST",
    url: "server/api.php",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    beforeSend: function () {
      // This function is called before the request is sent
      // You can use it to perform tasks like showing a loading spinner
      $("#loadingButton").removeClass("d-none");
    },
    success: function (res) {
      if (res.status == 422) {
        $("#errorMessage").removeClass("d-none");
        $("#errorMessage").text(res.message);
      } else if (res.status == 400) {
        $("#referralError").removeClass("d-none");
        $("#referralError").text(res.message);
      } else if (res.status == 200) {
        $("#createReferral")[0].reset();
        $("#createReferralModal").modal("hide");

        $("#referralsTable").load(location.href + " #referralsTable");
      }
    },
    error: function (xhr, status, error) {
      // This function is called if the request encounters an error
      console.error("Error:", status, error);
    },
    complete: function () {
      $("#loadingButton").addClass("d-none");
      $("#submitButton").removeClass("d-none");
    },
  });
});

$(document).on("click", ".viewMyRecord", function () {
  var rffrl_id = $(this).val();
  $.ajax({
    type: "GET",
    url: "server/api.php?myrecord_rffrl_id=" + rffrl_id,
    success: function (response) {
      var res = jQuery.parseJSON(response);
      if (res.status == 422) {
        alert(res.message);
      } else if (res.status == 200) {
        $("#fclt_name").text(res.data.fclt_name);
        $("#rffrl_id").val(res.data.rfrrl_id);
        $("#referralModal").modal("show");
        $("#view_name").val(res.data.name);
        $("#view_age").val(res.data.age);

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

          if (status) {
            audit.classList.remove("d-none");
            var pElement = $("<p></p>"); // Create a new <p> element
            pElement.text(status + " by " + fclt_name + " at " + time); // Include the label
            referralTransactionsDiv.append(pElement); // Append the <p> element to the div
          }
        }
      }
    },
  });
});
