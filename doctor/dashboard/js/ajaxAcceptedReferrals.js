document.addEventListener("DOMContentLoaded", () => {
  $(document).on("click", ".viewRecord", function () {
    var rffrl_id = $(this).val();
    $.ajax({
      type: "GET",
      url: "server/new_function.php?rffrl_id=" + rffrl_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#view_fclt_name").text(res.data.fclt_name);
          $("#view_rffrl_id").val(res.data.rfrrl_id);
          $("#view_name").val(res.data.name);
          $("#view_age").val(res.data.age);
          $("#view_sex").val(res.data.sex);
          $("#view_bdate").val(res.data.bdate);
          $("#view_address").val(res.data.address);
          $("#view_admitting_dx").val(res.data.admitting_dx);
          $("#view_rtpcr").val(res.data.rtpcr);
          $("#view_antigen").val(res.data.antigen);
          $("#view_clinical_ssx").val(res.data.clinical_ssx);
          $("#view_exposure_to_covid").val(res.data.exposure_to_covid);
          $("#view_temp").val(res.data.temp);
          $("#view_hr").val(res.data.hr);
          $("#view_resp").val(res.data.resp);
          $("#view_bp").val(res.data.bp);
          $("#view_02sat").val(res.data.O2sat);
          $("#view_02aided").val(res.data.O2aided);
          $("#view_procedures_need").val(res.data.procedures_need);
          $("#view_fh").val(res.data.fh);
          $("#view_ie").val(res.data.ie);
          $("#view_fht").val(res.data.fht);
          $("#view_lmp").val(res.data.lmp);
          $("#view_edc").val(res.data.edc);
          $("#view_aog").val(res.data.aog);
          $("#view_utz").val(res.data.utz);
          $("#view_utz_aog").val(res.data.utz_aog);
          $("#view_edd").val(res.data.edd);
          $("#view_enterpretation").val(res.data.enterpretation);
          $("#view_diagnostic_test").val(res.data.diagnostic_test);
          $("#view_view_referral_reason").val(res.data.referral_reason);
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
});
