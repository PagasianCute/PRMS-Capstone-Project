document.addEventListener("DOMContentLoaded", function () {
  const result = document.querySelector(".result-content");
  const form = document.querySelector("#report-form");

  $(document).on("submit", "#report-form", function (e) {
      e.preventDefault();

      const dateFromValue = $('#date-from').val();
      const dateToValue = $('#date-to').val();
      const statusValue = $('#selected-status').val();

      $.ajax({
          type: "POST",
          url: "server/generateReport.php",
          data: new FormData(form),
          processData: false,
          contentType: false,
          success: function (data) {
              result.innerHTML = data;
              $("#print-btn").attr("href", "server/generateReportRecord.php?date_from=" + dateFromValue + "&date_to=" + dateToValue + "&status=" + statusValue);
          }
      });
  });
});
