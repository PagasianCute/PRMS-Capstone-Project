document.addEventListener("DOMContentLoaded", function () {
  var fclt_channel = pusher.subscribe(fclt_id);
  var user_channel = pusher.subscribe(user_id);
  var expected_time;

  user_channel.bind("referral_sent", function (data) {
    $(".newReferrals").load(location.href + " .newReferrals");
  });
  const rightBtn = document.querySelectorAll(".nav-link.right-button");
  const leftBtn = document.querySelectorAll(".nav-link.left-button");
  var selectElement = document.getElementById("referralRecordsCount");
  var patientID;

  $(document).on("click", ".viewRecord", function () {
    var rffrl_id = $(this).val();
    $("#viewBtnClose").addClass("d-none");
    $("#accept_button").removeClass("d-none");

    if (fclt_type == "Hospital") {
      $("#decline_referral").addClass("d-none");
    } else if (fclt_type == "Provincial Hospital") {
      $("#decline_referral").removeClass("d-none");
    } else {
      $("#decline_referral").removeClass("d-none");
    }
    $.ajax({
      type: "GET",
      url: "server/new_referrals_function.php?rffrl_id=" + rffrl_id,
      success: function (response) {

        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          alert(res.message);
        } else if (res.status == 200) {
          $("#address1").val(res.data.municipality);
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
          $(".referral-reason").addClass("d-none");
          $("#cancel_button").addClass("d-none");
          $("#decline_button").addClass("d-none");
          $("#referralModal").modal("show");
          patientID = res.data.patients_id;
          ``;
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
            
              // Create and append the <h5> element
              var headingElement = $("<h5></h5>"); // Create a new <h5> element
              headingElement.text("Referral Audit"); // Set its text content
              referralTransactionsDiv.empty().append(headingElement); // Add the <h5> to the div
            
              // Create a new <p> element
              var pElement = $("<p></p>");
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

          function getActiveTabs() {
            let rightTab = document
              .querySelector(".nav-link.right-button.active")
              .getAttribute("data-tab");
            let leftTab = document
              .querySelector(".nav-link.left-button.active")
              .getAttribute("data-tab");

            return { leftTab, rightTab };
          }

          function executeGetTrimesterData() {
            var { leftTab, rightTab } = getActiveTabs();
            getTrimesterData(leftTab, rightTab, patientID, "");
            getPatientRecordsCount(patientID);
            getBirthExData(patientID, "");
            getPatientDetailsData(patientID, "");
          }

          // Execute getTrimesterData when viewMyRecord is clicked
          executeGetTrimesterData();

          // Usage in your event listeners
          rightBtn.forEach((button) => {
            button.addEventListener("click", function () {
              var { leftTab, rightTab } = getActiveTabs();
              var selectedValue = selectElement.value;
              getTrimesterData(leftTab, rightTab, patientID, selectedValue);
            });
          });

          leftBtn.forEach((button) => {
            button.addEventListener("click", function () {
              var { leftTab, rightTab } = getActiveTabs();
              var selectedValue = selectElement.value;
              getTrimesterData(leftTab, rightTab, patientID, selectedValue);
            });
          });

          selectElement.addEventListener("change", function () {
            var { leftTab, rightTab } = getActiveTabs();
            var selectedValue = selectElement.value;
            getTrimesterData(leftTab, rightTab, patientID, selectedValue);
            getBirthExData(patientID, selectedValue);
            getPatientDetailsData(patientID, selectedValue);
          });

          $(document).on("click", "#decline_referral", function () {
            $(".referral-reason").removeClass("d-none");
            $("#decline_referral").addClass("d-none");
            $("#accept_button").addClass("d-none");
            $("#decline_button").removeClass("d-none");
            $("#cancel_button").removeClass("d-none");
          });

          $(document).on("click", "#cancel_button", function () {
            $(".referral-reason").addClass("d-none");
            $("#cancel_button").addClass("d-none");
            $("#decline_button").addClass("d-none");
            $("#decline_referral").removeClass("d-none");
            $("#accept_button").removeClass("d-none");
          });

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

  function getPatientRecordsCount(records_id) {
    var recordsSelect = $("#referralRecordsCount");

    // Reset the selected option to the default (first option)
    recordsSelect.prop("selectedIndex", 0);

    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_count_id: records_id,
      },
      success: function (response) {
        var res = JSON.parse(response);
        if (res.status == 200) {
          updatePatientRecordsDropdown(recordsSelect, res.data);
        } else if (res.status == 404) {
          updatePatientRecordsDropdown(recordsSelect, []);
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
        var res = JSON.parse(response);
        if (res.status == 200) {
          $("#patientInformation-tab").removeClass("disabled");
          $("#petsa_ng_unang_checkup").val(
            formatDate(res.data.petsa_ng_unang_checkup)
          );
          $("#edad").val(res.data.edad);
          $("#timbang").val(res.data.timbang);
          $("#taas").val(res.data.taas);
          $("#kalagayan_ng_kalusugan").val(res.data.kalagayan_ng_kalusugan);
          $("#petsa_ng_huling_regla").val(
            formatDate(res.data.petsa_ng_huling_regla)
          );
          $("#kailan_ako_manganganak").val(
            formatDate(res.data.kailan_ako_manganganak)
          );
          $("#pang_ilang_pagbubuntis").val(res.data.pang_ilang_pagbubuntis);
        } else if (res.status == 404) {
          $(".patient-info-form input").val("");
          $("#patientInformation-tab").addClass("disabled");
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
        var res = JSON.parse(response);
        if (res.status == 200) {
          $("#birthExperience-tab").removeClass("disabled");
          $("#date_of_delivery").val(formatDate(res.data.date_of_delivery));
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
        } else if (res.status == 404) {
          $(".birthExp-form input").val("");
          $("#birthExperience-tab").addClass("disabled");
        }
      },
    });
  }

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
        var res = JSON.parse(response);
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
        // Handle data for the second query
        if (res.data2.status == 201) {
          $("#trimesters-tab").removeClass("disabled");
        } else if (res.data2.status == 405) {
          $("#trimesters-tab").addClass("disabled");
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
        }
        // Check for the overall status of the response
        if (res.data1.status == 200) {
          $("#trimesters-tab").removeClass("disabled");
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
          $("#address1").val(res.data.municipality);
          $("#fclt_name").text(res.data.fclt_name);
          $("#fclt_id").val(res.data.fclt_id);
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
          $(".referral-reason").addClass("d-none");
          $("#cancel_button").addClass("d-none");
          $("#decline_button").addClass("d-none");
          $("#referralModal").modal("show");
          patientID = res.data.patients_id;
          console.log(patientID);

          function getActiveTabs() {
            let rightTab = document
              .querySelector(".nav-link.right-button.active")
              .getAttribute("data-tab");
            let leftTab = document
              .querySelector(".nav-link.left-button.active")
              .getAttribute("data-tab");

            return { leftTab, rightTab };
          }

          function executeGetTrimesterData() {
            var { leftTab, rightTab } = getActiveTabs();
            getTrimesterData(leftTab, rightTab, patientID, "");
            getPatientRecordsCount(patientID);
            getBirthExData(patientID, "");
            getPatientDetailsData(patientID, "");
          }

          // Execute getTrimesterData when viewMyRecord is clicked
          executeGetTrimesterData();

          // Usage in your event listeners
          rightBtn.forEach((button) => {
            button.addEventListener("click", function () {
              var { leftTab, rightTab } = getActiveTabs();
              var selectedValue = selectElement.value;
              getTrimesterData(leftTab, rightTab, patientID, selectedValue);
            });
          });

          leftBtn.forEach((button) => {
            button.addEventListener("click", function () {
              var { leftTab, rightTab } = getActiveTabs();
              var selectedValue = selectElement.value;
              getTrimesterData(leftTab, rightTab, patientID, selectedValue);
            });
          });

          selectElement.addEventListener("change", function () {
            var { leftTab, rightTab } = getActiveTabs();
            var selectedValue = selectElement.value;
            getTrimesterData(leftTab, rightTab, patientID, selectedValue);
            getBirthExData(patientID, selectedValue);
            getPatientDetailsData(patientID, selectedValue);
          });
        }
      },
    });
    $("#viewBtnClose").removeClass("d-none");
    $("#accept_button").addClass("d-none");
    $("#decline_referral").addClass("d-none");
  });

  $(document).on("click", "#decline_button", function () {
    var formData = new FormData($("#referral_form")[0]);
    formData.append("decline_referral", true);

    $.ajax({
      type: "POST",
      url: "server/new_function.php",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $(".decline-loading").removeClass("d-none");
        $("#decline_button").addClass("disabled");
        $(".decline-span").addClass("d-none");
      },
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          $("#referralModal").modal("hide");
          $(".newReferrals").load(location.href + " .newReferrals");
          $("#reason").val("");
        } else if (res.status == 422) {
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        }
      },
      complete: function () {
        $(".decline-loading").addClass("d-none");
        $("#decline_button").removeClass("disabled");
        $(".decline-span").removeClass("d-none");
      },
    });
  });


  $(document).on("click", "#accept_button", async function () {
    var formData = new FormData($("#referral_form")[0]);
    formData.append("accept_referral", true);

    try {

      const travelTime = await computeTravelTime();

      // Add the travel time to the form data
      formData.append("expected_time", travelTime.expected_Time);
      formData.append("travel_time", travelTime.travel_Time);

      // Continue with your AJAX call
      $.ajax({
        type: "POST",
        url: "server/new_function.php",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
          // Show loading indicator before making the AJAX call
          //$(".accept-loading").removeClass("d-none");
          $("#accept_button").addClass("disabled");
          $(".accept-span").addClass("d-none");
        },
        success: function (response) {
          console.log(response);
          var res = jQuery.parseJSON(response);
          if (res.status == 200) {
            $("#referralModal").modal("hide");
            $(".newReferrals").load(location.href + " .newReferrals");
            $(".newArrivals").load(location.href + " .newArrivals");
            $("#reason").val("");

            // Open the map modal
            $("#TravelTime").modal("show");
          }
        },
        complete: function () {
          // Hide the loading indicator after the AJAX call is complete
          //$(".accept-loading").addClass("d-none");
          $("#accept_button").removeClass("disabled");
          $(".accept-span").removeClass("d-none");
          $(".travel-loading").addClass("d-none");

          // Update the map in the modal after the modal is fully visible
          $("#TravelTime").on("shown.bs.modal", function () {
            updateMap();
          });
        },
      });
    } catch (error) {
      console.error("Error getting travel time:", error.message);
    }
  });

  $(document).on("click", ".patientArrived", function () {
    var rffrl_id = $(this).val();
    $("#arrivalModal").modal("show");

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
            $(".newArrivals").load(location.href + " .newArrivals");
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
});

// Define mapInModal and tileLayer globally
const mapInModal = L.map("map").setView([7.1097, 125.6087], 7);

const tileLayerInModal = L.tileLayer(
  "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    maxZoom: 19,
    attribution: "© OpenStreetMap",
  }
).addTo(mapInModal);

let marker1, marker2, route;

// Function to handle the map update
function updateMap() {
  mapInModal.invalidateSize();
}

// Remove existing map elements when modal is hidden
$("#TravelTime").on("hidden.bs.modal", function () {
  // Remove existing markers and route
  if (marker1) mapInModal.removeLayer(marker1);
  if (marker2) mapInModal.removeLayer(marker2);
  if (route) mapInModal.removeLayer(route);

  // Reset map view
  mapInModal.setView([7.1097, 125.6087], 7);
});

async function geocodeAddress(address) {
  const response = await fetch(
    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
      address
    )}`
  );
  const data = await response.json();
  if (data.length > 0) {
    return {
      latitude: parseFloat(data[0].lat),
      longitude: parseFloat(data[0].lon),
    };
  } else {
    throw new Error("Invalid address");
  }
}

async function computeTravelTime() {
  try {
    // Show loading indicator, disable button, and hide span
    //$(".accept-loading").addClass("d-none");
    $(".travel-loading").removeClass("d-none");
    $("#accept_button").addClass("disabled");
    $(".accept-span").addClass("d-none");

    const address1 = document.getElementById("address1").value.trim();
    const address2 = document.getElementById("address2").value.trim();

    const startCoords = await geocodeAddress(address1);
    const endCoords = await geocodeAddress(address2);

    const proxyUrl = "proxy.php?url=";
    const apiKey = "5b3ce3597851110001cf624829013ac2b6424147ac045ef562822331";
    const directionsUrl = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${startCoords.longitude},${startCoords.latitude}&end=${endCoords.longitude},${endCoords.latitude}`;

    const response = await fetch(proxyUrl + encodeURIComponent(directionsUrl), {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const data = await response.json();

    // Validate response structure
    if (data && data.features && data.features.length > 0) {
      const travelTime = data.features[0].properties.segments[0].duration / 60; // in minutes
      console.log("Computed Travel Time:", travelTime);

      $("#expectedTime").val(formatTravelTime(travelTime));
      expected_time = formatTravelTime(travelTime);

      // Remove existing markers and route
      if (marker1) mapInModal.removeLayer(marker1);
      if (marker2) mapInModal.removeLayer(marker2);
      if (route) mapInModal.removeLayer(route);

      // Add new markers and route
      marker1 = L.marker([startCoords.latitude, startCoords.longitude]).addTo(mapInModal);
      marker2 = L.marker([endCoords.latitude, endCoords.longitude]).addTo(mapInModal);

      const coordinates = data.features[0].geometry.coordinates.map((coord) => [
        coord[1],
        coord[0],
      ]);
      route = L.polyline(coordinates, { color: "blue" }).addTo(mapInModal);

      // Center the map between the two markers
      mapInModal.setView(
        [
          (startCoords.latitude + endCoords.latitude) / 2,
          (startCoords.longitude + endCoords.longitude) / 2,
        ],
        11
      );

      // Hide loading indicator, enable button, and show span
      //$(".accept-loading").removeClass("d-none");
      $(".travel-loading").addClass("d-none");
      //$("#accept_button").removeClass("disabled");
      //$(".accept-span").removeClass("d-none");
      $("#expected-time-span").text(addTravelTimeToCurrentTime2(travelTime));
      $("#travel-time-span").text(formatTravelTime(travelTime));

      return {
        expected_Time: addTravelTimeToCurrentTime(travelTime),
        travel_Time: formatTravelTime(travelTime),
      };
    } else {
      // Log information about expected fallback
      console.info("No valid route found. Falling back to fixed travel time.");
      throw new Error("No valid route data available");
    }
  } catch (error) {
    console.warn("Error computing travel time:", error.message);

    // Fallback: Fixed Travel Time for Non-Traversable Routes
    const fallbackTravelTime = 5 * 60 + 4; // 5 hours and 4 minutes in minutes
    console.log("Fallback Travel Time:", fallbackTravelTime);

    $("#expectedTime").val(formatTravelTime(fallbackTravelTime));
    $("#expected-time-span").text(addTravelTimeToCurrentTime2(fallbackTravelTime));
    $("#travel-time-span").text(formatTravelTime(fallbackTravelTime));

    // Return fixed travel time
    return {
      expected_Time: addTravelTimeToCurrentTime(fallbackTravelTime),
      travel_Time: formatTravelTime(fallbackTravelTime),
    };
  }
}


function calculateStraightLineDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // Radius of Earth in kilometers
  const dLat = toRadians(lat2 - lat1);
  const dLon = toRadians(lon2 - lon1);
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(toRadians(lat1)) *
      Math.cos(toRadians(lat2)) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c; // Distance in kilometers
}

function toRadians(degrees) {
  return (degrees * Math.PI) / 180;
}

function addTravelTimeToCurrentTime(travelTimeInMinutes) {
  let currentTime = new Date();
  let travelTimeInMilliseconds = travelTimeInMinutes * 60 * 1000;
  let expectedTime = new Date(currentTime.getTime() + travelTimeInMilliseconds);

  let formattedTime = expectedTime.toLocaleTimeString("en-US", {
    hour12: false,
    hour: "2-digit",
    minute: "2-digit",
  });

  return formattedTime.replace(/\.\d+$/, "");
}

function addTravelTimeToCurrentTime2(travelTimeInMinutes) {
  let currentTime = new Date();
  let options = { timeZone: "Asia/Manila" };
  let travelTimeInMilliseconds = travelTimeInMinutes * 60 * 1000;
  let expectedTime = new Date(currentTime.getTime() + travelTimeInMilliseconds);

  return expectedTime.toLocaleTimeString("en-US", {
    ...options,
    hour12: true,
    hour: "numeric",
    minute: "2-digit",
  });
}

function formatTravelTime(durationInMinutes) {
  const hours = Math.floor(durationInMinutes / 60);
  const minutes = durationInMinutes % 60;

  if (hours > 0) {
    return `${hours} hour${hours > 1 ? "s" : ""} ${minutes.toFixed(2)} minutes`;
  } else {
    return `${minutes.toFixed(2)} minutes`;
  }
}
