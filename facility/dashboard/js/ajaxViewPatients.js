document.addEventListener("DOMContentLoaded", () => {
  let rightTab = document
    .querySelector(".nav-link.right-button.active")
    .getAttribute("data-tab");
  let leftTab = document
    .querySelector(".nav-link.left-button.active")
    .getAttribute("data-tab");
  const rightBtn = document.querySelectorAll(".nav-link.right-button");
  const leftBtn = document.querySelectorAll(".nav-link.left-button");
  const urlParams = new URLSearchParams(window.location.search);
  var selectElement = document.getElementById("recordsCount");
  const patientID = urlParams.get("id");
  const record = urlParams.get("record");

  selectElement.addEventListener("change", function () {
    var selectedValue = selectElement.value;

    console.log(selectedValue);
    if (selectedValue) {
      urlParams.set("record", selectedValue);
    } else {
      urlParams.delete("record");
    }
    var newURL =
      window.location.origin +
      window.location.pathname +
      "?" +
      urlParams.toString();
    window.history.replaceState({}, document.title, newURL);

    getPatientDetailsData(patientID, selectedValue);
    getBirthExData(patientID, selectedValue);
    getTrimesterData(leftTab, rightTab, patientID, selectedValue);
    location.reload();
  });

  if (record == null) {
    empty();
  } else {
    withrecord();
  }

  function empty() {
    getBirthExData(patientID, "");
    getPatientDetailsData(patientID, "");
    getTrimesterData(leftTab, rightTab, patientID, "");
  }
  function withrecord() {
    getBirthExData(patientID, record);
    getPatientDetailsData(patientID, record);
    getTrimesterData(leftTab, rightTab, patientID, record);
  }

  rightBtn.forEach((button) => {
    button.addEventListener("click", function () {
      rightTab = this.getAttribute("data-tab");
      var selectedValue = selectElement.value;
      getTrimesterData(leftTab, rightTab, patientID, selectedValue);
    });
  });

  leftBtn.forEach((button) => {
    button.addEventListener("click", function () {
      leftTab = this.getAttribute("data-tab");
      var selectedValue = selectElement.value;
      getTrimesterData(leftTab, rightTab, patientID, selectedValue);
    });
  });

  getPatientRecordsCount(patientID);

  function getTrimesterData(trimester, checkup, patient_id, recordsCount) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        trimester_table: trimester,
        patientid: patient_id,
        check_up: checkup,
        records_count: recordsCount,
      },
      success: function (response) {
        var res = jQuery.parseJSON(response);
        console.log(res.data1.status);
        if (res.data1.status == 200) {
          var data1 = res.data1.data;
          if (res.data1.table == "first_trimester") {
            $("#firstTri_date").val(formatDate(data1.date));
            $("#firstTri_weight").val(data1.weight);
            $("#firstTri_height").val(data1.height);
            $("#firstTri_age_of_gestation").val(data1.age_of_gestation);
            $("#firstTri_blood_pressure").val(data1.blood_pressure);
            $("#firstTri_nutritional_status").val(data1.nutritional_status);
            $("#firstTri_laboratory_tests_done").val(
              data1.laboratory_tests_done
            );
            $("#firstTri_hemoglobin_count").val(data1.hemoglobin_count);
            $("#firstTri_urinalysis").val(data1.urinalysis);
            $("#firstTri_complete_blood_count").val(data1.complete_blood_count);
            $("#firstTri_stis_using_a_syndromic_approach").val(
              data1.stis_using_a_syndromic_approach
            );
            $("#firstTri_tetanus_containing_vaccine").val(
              data1.tetanus_containing_vaccine
            );
            $("#firstTri_given_services").val(data1.given_services);
            $("#firstTri_date_of_return").val(formatDate(data1.date_of_return));
            $("#firstTri_health_provider_name").val(data1.health_provider_name);
            $("#firstTri_hospital_referral").val(data1.hospital_referral);
          } else if (res.data1.table == "second_trimester") {
            $("#secondTri_date").val(formatDate(data1.date));
            $("#secondTri_weight").val(data1.weight);
            $("#secondTri_height").val(data1.height);
            $("#secondTri_age_of_gestation").val(data1.age_of_gestation);
            $("#secondTri_blood_pressure").val(data1.blood_pressure);
            $("#secondTri_nutritional_status").val(data1.nutritional_status);
            $("#secondTri_given_advise").val(data1.given_advise);
            $("#secondTri_laboratory_tests_done").val(
              data1.laboratory_tests_done
            );
            $("#secondTri_urinalysis").val(data1.urinalysis);
            $("#secondTri_complete_blood_count").val(
              data1.complete_blood_count
            );
            $("#secondTri_given_services").val(data1.given_services);
            $("#secondTri_date_of_return").val(
              formatDate(data1.date_of_return)
            );
            $("#secondTri_health_provider_name").val(
              data1.health_provider_name
            );
            $("#secondTri_hospital_referral").val(data1.hospital_referral);
          } else if (res.data1.table == "third_trimester") {
            $("#thirdTri_date").val(formatDate(data1.date));
            $("#thirdTri_weight").val(data1.weight);
            $("#thirdTri_height").val(data1.height);
            $("#thirdTri_age_of_gestation").val(data1.age_of_gestation);
            $("#thirdTri_blood_pressure").val(data1.blood_pressure);
            $("#thirdTri_nutritional_status").val(data1.nutritional_status);
            $("#thirdTri_given_advise").val(data1.given_advise);
            $("#thirdTri_laboratory_tests_done").val(
              data1.laboratory_tests_done
            );
            $("#thirdTri_urinalysis").val(data1.urinalysis);
            $("#thirdTri_complete_blood_count").val(data1.complete_blood_count);
            $("#thirdTri_given_services").val(data1.given_services);
            $("#thirdTri_date_of_return").val(formatDate(data1.date_of_return));
            $("#thirdTri_health_provider_name").val(data1.health_provider_name);
            $("#thirdTri_hospital_referral").val(data1.hospital_referral);
          }
        } else if (res.data1.status == 404) {
          $(".trimester-form input").val("");
        }
      },
    });
  }

  function getBirthExData(patient_id, recordsCount) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_id: patient_id,
        records_count: recordsCount,
      },
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          $("#date_of_delivery").val(res.data.date_of_delivery);
          $("#type_of_delivery").val(res.data.type_of_delivery);
          $("#birth_outcome").val(res.data.birth_outcome);
          $("#number_of_children_delivered").val(
            res.data.number_of_children_delivered
          );
          $("#pregnancy_hypertension").val(res.data.pregnancy_hypertension);
          $("#preeclampsia_eclampsia").val(res.data.preeclampsia_eclampsia);
          $("#bleeding_during_pregnancy").val(
            res.data.bleeding_during_pregnancy
          );
          $("#birthExSave").hide();
          $("#birthExUpdate").show();
        } else if (res.status == 404) {
          $("#date_of_delivery").val("");
        }
      },
    });
  }

  function getPatientDetailsData(patient_id, recordsCount) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_details_id: patient_id,
        records_count: recordsCount,
      },
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          $("#petsa_ng_unang_checkup").val(res.data.petsa_ng_unang_checkup);
          $("#edad").val(res.data.edad);
          $("#timbang").val(res.data.timbang);
          $("#taas").val(res.data.taas);
          $("#kalagayan_ng_kalusugan").val(res.data.kalagayan_ng_kalusugan);
          $("#petsa_ng_huling_regla").val(res.data.petsa_ng_huling_regla);
          $("#kailan_ako_manganganak").val(res.data.kailan_ako_manganganak);
          $("#pang_ilang_pagbubuntis").val(res.data.pang_ilang_pagbubuntis);
          $("#patientSave").addClass("d-none");
          $("#patientUpdate").removeClass("d-none");
        } else if (res.status == 404) {
          $("#petsa_ng_unang_checkup").val("");
        }
      },
    });
  }

  function getPatientRecordsCount(records_id) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_count_id: records_id,
      },
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          var recordsSelect = $("#recordsCount");

          recordsSelect.empty();

          var lastRecord = null;

          // Loop through the data and add options
          res.data.forEach(function (record, index, array) {
            var label = "Record " + record.records_count;

            if (index === array.length - 1) {
              label += " (Latest)";
              lastRecord = record.records_count;
            }
            recordsSelect.append(
              '<option value="' +
                record.records_count +
                '">' +
                label +
                "</option>"
            );
          });

          if (record == null) {
            if (lastRecord !== null) {
              recordsSelect
                .find('option[value="' + lastRecord + '"]')
                .prop("selected", true);
              console.log("asd" + lastRecord);
              getPatientNote(patientID, lastRecord);
            }
          } else {
            recordsSelect
              .find('option[value="' + record + '"]')
              .prop("selected", true);
            console.log("asd" + record);
            getPatientNote(patientID, record);
          }
        } else if (res.status == 404) {
          var recordsSelect = $("#recordsCount");
          recordsSelect.append("<option selected>No Records</option>");
        }
      },
    });
  }

  function getPatientNote(patient_id, recordsCount) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_note_id: patient_id,
        record: recordsCount,
      },
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          $("#note").text(res.data.note);
        } else if (res.status == 404) {
          console.log(res);
        }
      },
    });
  }

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
  $(document).on("submit", "#patient_note", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append("add_patient_note", true);
    formData.append("patients_id", patientID);
    formData.append("record", selectElement.value);

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
        } else if (res.status == 200) {
          console.log(res.message);
          showToast("Note", res.message);
        }
      },
    });
  });

  $(document).on("click", "#fileButton", function (event) {
    event.preventDefault();
    $("#attachment").click();
  });

  $(document).ready(function () {
    var showToastAfterReload = localStorage.getItem("showToastAfterReload");

    if (showToastAfterReload) {
      // Page is being reloaded, show toast
      showToast(localStorage.getItem("uploadedFileName"), "Attachment Added");
      // Clear the flag and filename in local storage
      localStorage.removeItem("showToastAfterReload");
      localStorage.removeItem("uploadedFileName");
    }

    $(document).on("change", "#attachment", function (event) {
      event.preventDefault();
      var files = $(this).prop("files");

      if (files.length > 0) {
        var formData = new FormData();
        formData.append("patients_id", patientID);

        for (var i = 0; i < files.length; i++) {
          formData.append("files[]", files[i]);
        }

        $.ajax({
          url: "server/upload_attachments.php",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            var fileResponses = JSON.parse(response);

            var hasErrors = fileResponses.some(function (response) {
              return response.startsWith("Error");
            });

            if (hasErrors) {
              showToast("Error uploading files:\n", fileResponses.join("\n"));
            } else {
              console.log("Files uploaded successfully:", fileResponses);
              $("#filesDIv").load(location.href + " #filesDIv > *");

              // Get the first uploaded filename for demonstration
              var uploadedFileName = fileResponses[0].split('"')[1];

              // Set a flag and filename in local storage to show the toast after the page reloads
              localStorage.setItem("showToastAfterReload", true);
              localStorage.setItem("uploadedFileName", uploadedFileName);

              // Reload the page
              window.location.reload(true);
            }
          },
          error: function (error) {
            console.error("Error uploading files:", error);
            alert("Error uploading files. Please try again later.");
          },
        });
      }
    });
  });

  $(document).on("click", ".deleteAttachment", function (e) {
    e.preventDefault();

    if (confirm("Are you sure you want to delete this attachment?")) {
      var attachments_id = $(this).val();

      $.ajax({
        type: "POST",
        url: "server/prenatal_function.php",
        data: {
          delete_attachment: true,
          attachments_id: attachments_id,
        },
        success: function (response) {
          var res = jQuery.parseJSON(response);
          if (res.status == 500) {
            alert(res.message);
          } else {
            var deletedFileName = res.deletedFileName; // Get the deleted file name from the response
            $("#filesDIv").load(location.href + " #filesDIv > *");
            showToast(deletedFileName, "Attachment Deleted");
          }
        },
      });
    }
  });
});

$(document).on("click", ".viewPatient", function () {
  var rffrl_id = $(this).val();
  $.ajax({
    type: "GET",
    url: "server/prenatal_function.php?view_patient_id=" + rffrl_id,
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
        $("#viewPatientModal").modal("show");
      }
    },
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
          //$(".createNewPrenatalRecord").attr("href", "view_prenatal.php?id=" + res.data.id);
          $(".createNewPrenatalRecord").attr("data-patient-id", res.data.id);
          $(".createNewPrenatalRecord").attr(
            "href",
            "view_prenatal.php?id=" + res.data.id
          );
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
});
