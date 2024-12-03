document.addEventListener("DOMContentLoaded", function () {
    const rightBtn = document.querySelectorAll(".nav-link.right-button");
    const leftBtn = document.querySelectorAll(".nav-link.left-button");
    var selectElement2 = document.getElementById(
      "referralRecordsCount_referralCreate"
    );
    var patientID2;
  
    $(".referralCreateBtn").on("click", function () {
      $("#patientName").modal("show");
      $(".otherDiv").addClass("d-none");
    });
  
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
            showToast("Referral", "Referral Created successfully!");
            reloadNotifications();
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
  
    $(document).on("submit", "#createReferralForReferralPatient", function (e) {
      e.preventDefault();
  
      $("#submitButton").addClass("d-none");
  
      var formData = new FormData(this);
      formData.append("create_referral_new", true);
  
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
            $("#createReferralForReferralPatient")[0].reset();
            $("#createReferralModal").modal("hide");
            showToast("Referral", "Referral Created successfully!");
            reloadNotifications();
            $("#referralsTable").load(location.href + " #referralsTable");
          }
        },
        error: function (xhr, status, error) {
          console.error("XHR Response:", xhr.responseText);
          console.error("Status:", status);
          console.error("Error:", error);
        },
        complete: function () {
          $("#loadingButton").addClass("d-none");
          $("#submitButton").removeClass("d-none");
        },
      });
    });

  
    function updatePatientRecordsDropdown(recordsSelect, records) {
      recordsSelect.empty();
  
      if (records.length > 0) {
        $(".trimester").removeClass("d-none");
        $(".noRecords").addClass("d-none");
        $("#referralRecordsCount").removeClass("disabled");
        var lastRecord = records[records.length - 1].records_count;
  
        records.forEach(function (record, index) {
          var label = "Record " + record.records_count;
          if (index === records.length - 1) {
            label += " (Latest)";
          }
  
          recordsSelect.append(
            '<option value="' + record.records_count + '">' + label + "</option>"
          );
        });
  
        recordsSelect
          .find('option[value="' + lastRecord + '"]')
          .prop("selected", true);
      } else {
        recordsSelect.append("<option selected>No Records</option>");
        $(".trimester").addClass("d-none");
        $(".noRecords").removeClass("d-none");
        $("#referralRecordsCount").addClass("disabled");
      }
    }
  
    function getPatientRecordsCount2(records_id) {
      var recordsSelect2 = $("#referralRecordsCount_referralCreate");
  
      // Reset the selected option to the default (first option)
      recordsSelect2.prop("selectedIndex", 0);
  
      $.ajax({
        type: "GET",
        url: "server/prenatal_function.php",
        data: {
          patient_count_id: records_id,
        },
        success: function (response) {
          var res = JSON.parse(response);
          if (res.status == 200) {
            recordsSelect2.removeClass("disabled");
            updatePatientRecordsDropdown(recordsSelect2, res.data);
          } else if (res.status == 404) {
            updatePatientRecordsDropdown(recordsSelect2, []);
            recordsSelect2.addClass("disabled");
          }
        },
      });
    }
  
    function getTrimesterData2(trimester, checkup, patient_id, recordsCount) {
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
          var res = JSON.parse(response);
          if (res.data1.status == 200) {
            var data1 = res.data1.data;
            if (res.data1.table == "first_trimester") {
              $("#firstTri_date_referralCreate").val(formatDate(data1.date));
              $("#firstTri_weight_referralCreate").val(data1.weight);
              $("#firstTri_height_referralCreate").val(data1.height);
              $("#firstTri_age_of_gestation_referralCreate").val(
                data1.age_of_gestation
              );
              $("#firstTri_blood_pressure_referralCreate").val(
                data1.blood_pressure
              );
              $("#firstTri_nutritional_status_referralCreate").val(
                data1.nutritional_status
              );
              $("#firstTri_laboratory_tests_done_referralCreate").val(
                data1.laboratory_tests_done
              );
              $("#firstTri_hemoglobin_count_referralCreate").val(
                data1.hemoglobin_count
              );
              $("#firstTri_urinalysis_referralCreate").val(data1.urinalysis);
              $("#firstTri_complete_blood_count_referralCreate").val(
                data1.complete_blood_count
              );
              $("#firstTri_stis_using_a_syndromic_approach_referralCreate").val(
                data1.stis_using_a_syndromic_approach
              );
              $("#firstTri_tetanus_containing_vaccine_referralCreate").val(
                data1.tetanus_containing_vaccine
              );
              $("#firstTri_given_services_referralCreate").val(
                data1.given_services
              );
              $("#firstTri_date_of_return_referralCreate").val(
                formatDate(data1.date_of_return)
              );
              $("#firstTri_health_provider_name_referralCreate").val(
                data1.health_provider_name
              );
              $("#firstTri_hospital_referral_referralCreate").val(
                data1.hospital_referral
              );
            } else if (res.data1.table == "second_trimester") {
              $("#secondTri_date_referralCreate").val(formatDate(data1.date));
              $("#secondTri_weight_referralCreate").val(data1.weight);
              $("#secondTri_height_referralCreate").val(data1.height);
              $("#secondTri_age_of_gestation_referralCreate").val(
                data1.age_of_gestation
              );
              $("#secondTri_blood_pressure_referralCreate").val(
                data1.blood_pressure
              );
              $("#secondTri_nutritional_status_referralCreate").val(
                data1.nutritional_status
              );
              $("#secondTri_given_advise_referralCreate").val(data1.given_advise);
              $("#secondTri_laboratory_tests_done_referralCreate").val(
                data1.laboratory_tests_done
              );
              $("#secondTri_urinalysis_referralCreate").val(data1.urinalysis);
              $("#secondTri_complete_blood_count_referralCreate").val(
                data1.complete_blood_count
              );
              $("#secondTri_given_services_referralCreate").val(
                data1.given_services
              );
              $("#secondTri_date_of_return_referralCreate").val(
                formatDate(data1.date_of_return)
              );
              $("#secondTri_health_provider_name_referralCreate").val(
                data1.health_provider_name
              );
              $("#secondTri_hospital_referral_referralCreate").val(
                data1.hospital_referral
              );
            } else if (res.data1.table == "third_trimester") {
              $("#thirdTri_date_referralCreate").val(formatDate(data1.date));
              $("#thirdTri_weight_referralCreate").val(data1.weight);
              $("#thirdTri_height_referralCreate").val(data1.height);
              $("#thirdTri_age_of_gestation_referralCreate").val(
                data1.age_of_gestation
              );
              $("#thirdTri_blood_pressure_referralCreate").val(
                data1.blood_pressure
              );
              $("#thirdTri_nutritional_status_referralCreate").val(
                data1.nutritional_status
              );
              $("#thirdTri_given_advise_referralCreate").val(data1.given_advise);
              $("#thirdTri_laboratory_tests_done_referralCreate").val(
                data1.laboratory_tests_done
              );
              $("#thirdTri_urinalysis_referralCreate").val(data1.urinalysis);
              $("#thirdTri_complete_blood_count_referralCreate").val(
                data1.complete_blood_count
              );
              $("#thirdTri_given_services_referralCreate").val(
                data1.given_services
              );
              $("#thirdTri_date_of_return_referralCreate").val(
                formatDate(data1.date_of_return)
              );
              $("#thirdTri_health_provider_name_referralCreate").val(
                data1.health_provider_name
              );
              $("#thirdTri_hospital_referral_referralCreate").val(
                data1.hospital_referral
              );
            }
          } else if (res.data1.status == 404) {
            $(".trimester-form input").val("");
          }
          if (res.data2.status == 201) {
            $("#trimesters_create-tab").removeClass("disabled");
          } else if (res.data2.status == 405) {
            $("#trimesters_create-tab").addClass("disabled");
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
          }
          // Check for the overall status of the response
          if (res.data1.status == 200) {
            $("#trimesters_create-tab").removeClass("disabled");
          }
        },
      });
    }
  
    $("#noRecrds-btn").on("click", function () {
      $("#createReferral")[0].reset();
      $("#patient-error").addClass("d-none");
      $("#name").prop("readonly", false);
      $("#age").prop("readonly", false);
      $("#sex").prop("readonly", false);
      $("#bdate").prop("readonly", false);
      $("#address").prop("readonly", false);
      $(".trimester").addClass("d-none");
      $(".noRecords").removeClass("d-none");
      var selectElement2 = $("#referralRecordsCount_referralCreate");
      selectElement2.empty().append("<option selected>No Records</option>");
      $("#trimesters_create-tab").addClass("disabled");
      $("#patientInformation_create-tab").addClass("disabled");
      $("#birthExperience_create-tab").addClass("disabled");
      selectElement2.addClass("disabled");
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
  
    function getBirthExData_create(patient_id, recordsCount) {
      $.ajax({
        type: "GET",
        url: "server/prenatal_function.php",
        data: {
          patient_id: patient_id,
          records_count: recordsCount,
        },
        success: function (response) {
          var res = JSON.parse(response);
          if (res.status == 200) {
            $("#birthExperience_create-tab").removeClass("disabled");
            $("#date_of_delivery_create").val(
              formatDate(res.data.date_of_delivery)
            );
            $("#type_of_delivery_create").val(res.data.type_of_delivery);
            $("#birth_outcome_create").val(res.data.birth_outcome);
            $("#number_of_children_delivered_create").val(
              res.data.number_of_children_delivered
            );
            $("#pregnancy_hypertension_create").val(
              res.data.pregnancy_hypertension
            );
            $("#preeclampsia_eclampsia_create").val(
              res.data.preeclampsia_eclampsia
            );
            $("#bleeding_during_pregnancy_create").val(
              res.data.bleeding_during_pregnancy
            );
          } else if (res.status == 404) {
            $(".birthExp-form input").val("");
            $("#birthExperience_create-tab").addClass("disabled");
          }
        },
      });
    }
  
    function getPatientDetailsData_create(patient_id, recordsCount) {
      $.ajax({
        type: "GET",
        url: "server/prenatal_function.php",
        data: {
          patient_details_id: patient_id,
          records_count: recordsCount,
        },
        success: function (response) {
          var res = JSON.parse(response);
          if (res.status == 200) {
            $("#patientInformation_create-tab").removeClass("disabled");
            $("#petsa_ng_unang_checkup_create").val(
              formatDate(res.data.petsa_ng_unang_checkup)
            );
            $("#edad_create").val(res.data.edad);
            $("#timbang_create").val(res.data.timbang);
            $("#taas_create").val(res.data.taas);
            $("#kalagayan_ng_kalusugan_create").val(
              res.data.kalagayan_ng_kalusugan
            );
            $("#petsa_ng_huling_regla_create").val(
              formatDate(res.data.petsa_ng_huling_regla)
            );
            $("#kailan_ako_manganganak_create").val(
              formatDate(res.data.kailan_ako_manganganak)
            );
            $("#pang_ilang_pagbubuntis_create").val(
              res.data.pang_ilang_pagbubuntis
            );
          } else if (res.status == 404) {
            $(".patient-info-form input").val("");
            $("#patientInformation_create-tab").addClass("disabled");
          }
        },
      });
    }
    $("#referral_reason").on("change", function () {
      if ($(this).val() === "Other") {
        $(".otherDiv").removeClass("d-none");
      } else {
        $(".otherDiv").addClass("d-none");
      }
    });
  
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
    $("#referralModal").on("hidden.bs.modal", function () {
      // Set the "Referral Record" button as active
      $("#referralRecord-tab").addClass("active");
      // Remove the "active" class from other buttons
      $(
        "#patientInformation-tab, #birthExperience-tab, #trimesters-tab"
      ).removeClass("active");
  
      // Display the content of the "Referral Record" tab
      $("#referralRecord").addClass("show active");
      // Hide the content of other tabs
      $("#patientInformation, #birthExperience, #trimesters").removeClass(
        "show active"
      );
  
      // Optionally, reset the value of the select element
      $("#referralRecordsCount").val("");
    });
  
    $(document).on("click", ".createNewReferral", function (e) {
      e.preventDefault();
      var patients_id = $(this).val();
  
      const formData = new FormData();
      formData.append("get-patient-referred", true);
      formData.append("patient-id", patients_id);
  
      $.ajax({
        type: "POST",
        url: "server/new_function.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          try {
            var res = JSON.parse(response);
            if (res.status == 404) {
              console.log(res);
              alert("Error: " + res.message);
            } else if (res.status == 200) {
              $("#createReferralModal").modal("show");
              $("#name").val(
                res.data.lname + ", " + res.data.fname + " " + res.data.mname
              );
              $("#name").prop("readonly", true);
              $("#age").val(res.data.age);
              $("#age").prop("readonly", true);
              $("#sex").val(res.data.gender);
              $("#sex").prop("readonly", true);
              $("#bdate").val(res.data.birthdate);
              $("#bdate").prop("readonly", true);
              $("#address").val(res.data.barangay);
              $("#address").prop("readonly", true);
              $("#referral-patient-id").val(res.data.patients_id);
              $("#referred_hospital").val(res.data.referred_hospital);
              $("#referral-patient-record").val(res.data.record);
              patientID2 = res.data.id;
              getPatientRecordsCount2(patientID2);
              getBirthExData_create(patientID2, "");
              getPatientDetailsData_create(patientID2, "");
  
              function getActiveTabs() {
                let rightTab = document
                  .querySelector(".nav-link.referralCreate.right-button.active")
                  .getAttribute("data-tab");
                let leftTab = document
                  .querySelector(".nav-link.referralCreate.left-button.active")
                  .getAttribute("data-tab");
  
                return { leftTab, rightTab };
              }
  
              function executeGetTrimesterData() {
                var { leftTab, rightTab } = getActiveTabs();
                getTrimesterData2(leftTab, rightTab, patientID2, "");
              }
  
              // Execute getTrimesterData when viewMyRecord is clicked
              executeGetTrimesterData();
  
              // Usage in your event listeners
              rightBtn.forEach((button) => {
                button.addEventListener("click", function () {
                  var { leftTab, rightTab } = getActiveTabs();
                  var selectedValue2 = selectElement2.value;
                  getTrimesterData2(
                    leftTab,
                    rightTab,
                    patientID2,
                    selectedValue2
                  );
                });
              });
  
              leftBtn.forEach((button) => {
                button.addEventListener("click", function () {
                  var { leftTab, rightTab } = getActiveTabs();
                  var selectedValue2 = selectElement2.value;
                  getTrimesterData2(
                    leftTab,
                    rightTab,
                    patientID2,
                    selectedValue2
                  );
                  console.log(leftTab, rightTab);
                });
              });
  
              selectElement2.addEventListener("change", function () {
                var { leftTab, rightTab } = getActiveTabs();
                var selectedValue2 = selectElement2.value;
                getTrimesterData2(leftTab, rightTab, patientID2, selectedValue2);
                getBirthExData_create(patientID2, selectedValue2);
                getPatientDetailsData_create(patientID2, selectedValue2);
              });
            } else if (res.status == 422) {
              $("#patient-error").removeClass("d-none");
              $("#patient-error").text(res.message);
            }
          } catch (error) {
            console.error("Error parsing JSON:", error);
            alert("Error: Something went wrong. Please try again.");
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          alert("Error: Something went wrong. Please try again.");
        },
      });
    });
  
  });
  