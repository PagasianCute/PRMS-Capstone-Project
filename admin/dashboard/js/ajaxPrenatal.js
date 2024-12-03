document.addEventListener("DOMContentLoaded", function () {
  const recordsList = document.querySelector(".records .records-list");

  tooltip();

  function tooltip() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
      });
  }

$(document).on('click', '.viewPatient', function () {
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
                $('#view_fname').val(res.data.fname);
                $('#view_mname').val(res.data.mname);
                $('#view_lname').val(res.data.lname);
                $('#view_contactNum').val(res.data.contact);
                $('#view_address').val(res.data.address);
                $('#viewPatientModal').modal('show');
            }
        }
    });

    let xhr = new XMLHttpRequest();
        xhr.open("GET", "server/prenatal_function.php?get_patient_records=" + patient_id, true);
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

$(document).on('click', '.viewPatientRecords', function () {
    var patients_id = $(this).val();
    $.ajax({
        type: "GET",
        url: "server/prenatal_function.php?view_patient_records_id=" + patients_id,
        success: function (response) {
            var res = jQuery.parseJSON(response);
            if (res.status == 404) {
                $('#viewPatientRecordsModal').modal('show');
            } else if (res.status == 200) {
                //$(".createNewPrenatalRecord").attr("href", "view_prenatal.php?id=" + res.data.id);
                $(".createNewPrenatalRecord").attr("data-patient-id", res.data.id);
                $(".createNewPrenatalRecord").attr("href", "view_prenatal.php?id=" + res.data.id);
                $('#viewPatientRecordsModal').modal('show');
                
                $.ajax({
                type: "GET",
                url: "server/prenatal_function.php?patient_count_id=" + patients_id,
                success: function (response) {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 404) {
                        $('#viewPatientRecordsModal .modal-body').empty();
                        var alertHtml = '<div class="alert alert-primary d-flex records_alert" role="alert">' +
                                    '<h6>No Records</h6>' +
                                    '</div>';

                                // Append the alert to the modal body
                        $('#viewPatientRecordsModal .modal-body').append(alertHtml);

                    } else if (res.status == 200) {
                        $('#viewPatientRecordsModal .modal-body').empty();
                        
                        if (Array.isArray(res.data) && res.data.length > 0) {
                            // Loop through the data and append the alert for each item
                            res.data.forEach(function (item) {
                                var alertHtml = '<div class="alert alert-primary d-flex records_alert" role="alert">' +
                                    '<h6>Record ' + item.records_count + '</h6>' +
                                    '<a class="btn btn-primary" href="view_prenatal.php?id='+ item.patients_id +'&record='+ item.records_count +'" role="button">View</a>' +
                                    '</div>';

                                // Append the alert to the modal body
                                $('#viewPatientRecordsModal .modal-body').append(alertHtml);
                            });
                        }

                        // Show the modal
                        $('#viewPatientRecordsModal').modal('show');
                    }
                }
            });

            }
        }
    });
});

$(document).on('click', '.createNewPrenatalRecord', function () {
  var patients_id = $(this).data('patient-id'); 
    //alert(patients_id);
    $.ajax({
    type: "POST",
    url: "server/prenatal_function.php",
    data: {
        new_record: true,
        patients_id: patients_id
    },
    success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
        const record = urlParams.get("record");
        var recordNumber = parseInt(record, 10);
        var total = recordNumber + 1;

        if (total) {
            urlParams.set('record', total);
        } else {
            urlParams.delete('record');
        }
        var newURL = window.location.origin + window.location.pathname + '?' + urlParams.toString();
        window.history.replaceState({}, document.title, newURL);
        location.reload();

        } else if (res.status == 300) {
            $("#errorMessage").removeClass("d-none");
            $("#errorMessage").text(res.message);
        }
    },
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
          if (res.status == 500) {
            alert(res.message);
          } else {
            alert(res.message);
            $("#table").load(location.href + " #table");
            $("#fieldForm").load(location.href + " #fieldForm");
          }
        },
      });
    }
  });
});