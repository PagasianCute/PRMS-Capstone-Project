document.addEventListener("DOMContentLoaded", () => {

    let rightTab = document.querySelector(".nav-link.right-button.active").getAttribute("data-tab");
    let leftTab = document.querySelector(".nav-link.left-button.active").getAttribute("data-tab");
    const rightBtn = document.querySelectorAll(".nav-link.right-button");
    const leftBtn = document.querySelectorAll(".nav-link.left-button");
    const urlParams = new URLSearchParams(window.location.search);
    var selectElement = document.getElementById('recordsCount');
    const patientID = urlParams.get("id");
    const record = urlParams.get("record");

    $(document).ready(function () {
      $('.prenatal-datepicker').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true
      });
    });
    
    selectElement.addEventListener('change', function() {
        var selectedValue = selectElement.value;
        
        console.log(selectedValue);
        if (selectedValue) {
          urlParams.set('record', selectedValue);
        } else {
          urlParams.delete('record');
        }
        var newURL = window.location.origin + window.location.pathname + '?' + urlParams.toString();
        window.history.replaceState({}, document.title, newURL);
      
        getPatientDetailsData(patientID, selectedValue);
        getBirthExData(patientID, selectedValue);
        getTrimesterData(leftTab, rightTab, patientID, selectedValue);
    });
    
    if(record == null){
      empty();
    }else{
      withrecord();
    }

    function empty(){
        getBirthExData(patientID, '');
        getPatientDetailsData(patientID, '');
        getTrimesterData(leftTab, rightTab, patientID, '');
    }
    function withrecord(){
        getBirthExData(patientID, record);
        getPatientDetailsData(patientID, record);
        getTrimesterData(leftTab, rightTab, patientID, record);
    }
    
    $(document).on('click', '#createNewRecords', function () {
      $.ajax({
          type: "POST",
          url: "server/prenatal_function.php",
          data: {
              new_record: true,
              patients_id: patientID
          },
          success: function (response) {
              var res = jQuery.parseJSON(response);
              if (res.status == 200) {
                  urlParams.delete('record');
                  const updatedUrl = window.location.pathname + '?' + urlParams.toString();
                  window.history.replaceState({}, document.title, updatedUrl);
                  location.reload();
                  empty();
              } else if (res.status == 300) {
                  $("#errorMessage").removeClass("d-none");
                  $("#errorMessage").text(res.message);
              }
          },
      });
  });
    
    
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
        records_count: recordsCount
      },
      success: function (response) {
        var res = JSON.parse(response);
        if (res.table == "first_trimester") {
          $('#firstTri_date').val(res.data.date);
          $('#firstTri_date').datepicker('update');
          $('#firstTri_weight').val(res.data.weight);
          $('#firstTri_height').val(res.data.height);
          $('#firstTri_age_of_gestation').val(res.data.age_of_gestation);
          $('#firstTri_blood_pressure').val(res.data.blood_pressure);
          $('#firstTri_nutritional_status').val(res.data.nutritional_status);
          $('#firstTri_laboratory_tests_done').val(res.data.laboratory_tests_done);
          $('#firstTri_hemoglobin_count').val(res.data.hemoglobin_count);
          $('#firstTri_urinalysis').val(res.data.urinalysis);
          $('#firstTri_complete_blood_count').val(res.data.complete_blood_count);
          $('#firstTri_stis_using_a_syndromic_approach').val(res.data.stis_using_a_syndromic_approach);
          $('#firstTri_tetanus_containing_vaccine').val(res.data.tetanus_containing_vaccine);
          $('#firstTri_given_services').val(res.data.given_services);
          $('#firstTri_date_of_return').val(res.data.date_of_return);
          $('#firstTri_date_of_return').datepicker('update');
          $('#firstTri_health_provider_name').val(res.data.health_provider_name);
          $('#firstTri_hospital_referral').val(res.data.hospital_referral);
          $("#firstTriSave").hide();
          $("#firstTriUpdate").show();
        }else if(res.table == "second_trimester"){
          $('#secondTri_date').val(res.data.date);
          $('#secondTri_date').datepicker('update');
          $('#secondTri_weight').val(res.data.weight);
          $('#secondTri_height').val(res.data.height);
          $('#secondTri_age_of_gestation').val(res.data.age_of_gestation);
          $('#secondTri_blood_pressure').val(res.data.blood_pressure);
          $('#secondTri_nutritional_status').val(res.data.nutritional_status);
          $('#secondTri_given_advise').val(res.data.given_advise);
          $('#secondTri_laboratory_tests_done').val(res.data.laboratory_tests_done);
          $('#secondTri_urinalysis').val(res.data.urinalysis);
          $('#secondTri_complete_blood_count').val(res.data.complete_blood_count);
          $('#secondTri_given_services').val(res.data.given_services);
          $('#secondTri_date_of_return').val(res.data.date_of_return);
          $('#secondTri_date_of_return').datepicker('update');
          $('#secondTri_health_provider_name').val(res.data.health_provider_name);
          $('#secondTri_hospital_referral').val(res.data.hospital_referral);
          $("#secondTriSave").hide();
          $("#secondTriUpdate").show();
        }else if(res.table == "third_trimester"){
          $('#thirdTri_date').val(res.data.date);
          $('#thirdTri_date').datepicker('update');
          $('#thirdTri_weight').val(res.data.weight);
          $('#thirdTri_height').val(res.data.height);
          $('#thirdTri_age_of_gestation').val(res.data.age_of_gestation);
          $('#thirdTri_blood_pressure').val(res.data.blood_pressure);
          $('#thirdTri_nutritional_status').val(res.data.nutritional_status);
          $('#thirdTri_given_advise').val(res.data.given_advise);
          $('#thirdTri_laboratory_tests_done').val(res.data.laboratory_tests_done);
          $('#thirdTri_urinalysis').val(res.data.urinalysis);
          $('#thirdTri_complete_blood_count').val(res.data.complete_blood_count);
          $('#thirdTri_given_services').val(res.data.given_services);
          $('#thirdTri_date_of_return').val(res.data.date_of_return);
          $('#thirdTri_date_of_return').datepicker('update');
          $('#thirdTri_health_provider_name').val(res.data.health_provider_name);
          $('#thirdTri_hospital_referral').val(res.data.hospital_referral);
          $("#thirdTriSave").hide();
          $("#thirdTriUpdate").show();
          }else if(res.status == 404){
          $("#firstTriSave").show();
          $("#secondTriSave").show();
          $("#thirdTriSave").show();
          $("#firstTriUpdate").hide();
          $("#secondTriUpdate").hide();
          $("#thirdTriUpdate").hide();
          $("#firstTrimesterInsert")[0].reset();
          $('#firstTri_date').val('');
          $('#firstTri_date').datepicker('update');
          $('#secondTri_date').val('');
          $('#secondTri_date').datepicker('update');
          $('#thirdTri_date').val('');
          $('#thirdTri_date').datepicker('update');
          $("#secondTrimesterInsert")[0].reset();
          $("#thirdTrimesterInsert")[0].reset();
        }
      }
    });
    }
    
    function getBirthExData(patient_id, recordsCount) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_id: patient_id,
        records_count: recordsCount
      },
      success: function (response) {
        var res = JSON.parse(response);
        if (res.status == 200) {
          $('#date_of_delivery').val(res.data.date_of_delivery);
          $('#date_of_delivery').datepicker('update');
          $('#type_of_delivery').val(res.data.type_of_delivery);
          $('#birth_outcome').val(res.data.birth_outcome);
          $('#number_of_children_delivered').val(res.data.number_of_children_delivered);
          $('#pregnancy_hypertension').val(res.data.pregnancy_hypertension);
          $('#preeclampsia_eclampsia').val(res.data.preeclampsia_eclampsia);
          $('#bleeding_during_pregnancy').val(res.data.bleeding_during_pregnancy);
          $("#birthExSave").hide();
          $("#birthExUpdate").show();
        }else if(res.status == 404){
          $('#date_of_delivery').val('');
          $('#date_of_delivery').datepicker('update');
          $("#birthExperienceInsert")[0].reset();
          $("#birthExSave").show();
          $("#birthExUpdate").hide();
        }
      }
    });
    }
    
    function getPatientDetailsData(patient_id, recordsCount) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_details_id: patient_id,
        records_count: recordsCount
      },
      success: function (response) {
        var res = JSON.parse(response);
        if (res.status == 200) {
          $('#petsa_ng_unang_checkup').val(res.data.petsa_ng_unang_checkup);
          $('#petsa_ng_unang_checkup').datepicker('update');
          $('#edad').val(res.data.edad);
          $('#timbang').val(res.data.timbang);
          $('#taas').val(res.data.taas);
          $('#kalagayan_ng_kalusugan').val(res.data.kalagayan_ng_kalusugan);
          $('#petsa_ng_huling_regla').val(res.data.petsa_ng_huling_regla);
          $('#kailan_ako_manganganak').val(res.data.kailan_ako_manganganak);
          $('#pang_ilang_pagbubuntis').val(res.data.pang_ilang_pagbubuntis);
          $("#patientSave").addClass("d-none");
          $("#patientUpdate").removeClass("d-none");
        }else if(res.status == 404){
          $('#petsa_ng_unang_checkup').val('');
          $('#petsa_ng_unang_checkup').datepicker('update');
          $("#patientsDetailsInsert")[0].reset();
          $("#patientUpdate").addClass("d-none");
          $("#patientSave").removeClass("d-none");
        }
      }
    });
    }
    
    function getPatientRecordsCount(records_id) {
    $.ajax({
      type: "GET",
      url: "server/prenatal_function.php",
      data: {
        patient_count_id: records_id
      },
      success: function (response) {
        var res = JSON.parse(response);
        if (res.status == 200) {
          var recordsSelect = $('#recordsCount');

          recordsSelect.empty();
    
          var lastRecord = null;
    
          // Loop through the data and add options
          res.data.forEach(function(record, index, array) {
          var label = 'Record ' + record.records_count;
            
            if (index === array.length - 1) {
              label += ' (Latest)';
              lastRecord = record.records_count;
            }
            recordsSelect.append('<option value="' + record.records_count + '">' + label + '</option>');
          });
    
          if(record == null){
          if (lastRecord !== null) {
            recordsSelect.find('option[value="' + lastRecord + '"]').prop('selected', true);
          }
        }else{
          recordsSelect.find('option[value="' + record + '"]').prop('selected', true);
        }
        } else if(res.status == 404){
          var recordsSelect = $('#recordsCount');
          recordsSelect.append('<option selected>No Records</option>');
        }
      }
    });
    }
    
    $(document).on("submit", "#firstTrimesterInsert", function (e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var selectedValue = selectElement.value;
    
    formData.append("selected_row", selectedValue);
    formData.append("patient_id", patientID);
    formData.append("checkup", rightTab);
    
    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === 'firstTriSave') {
      formData.append("first_trimesters_insert", true);
    } else if (clickedButton.id === 'firstTriUpdate') {
      formData.append("first_trimesters_update", true);
    }
    
    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if(res.status == 200) {
          $("#firstTriSave").hide();
          $("#firstTriUpdate").show();
        } else if (res.status == 300) {
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        }
      },
    });
    });
    
    $(document).on("submit", "#secondTrimesterInsert", function (e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var selectedValue = selectElement.value;
    
    formData.append("selected_row", selectedValue);
    formData.append("patient_id", patientID);
    formData.append("checkup", rightTab);
    
    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === 'secondTriSave') {
      formData.append("second_trimesters_insert", true);
    } else if (clickedButton.id === 'secondTriUpdate') {
      formData.append("second_trimesters_update", true);
    }
    
    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          $("#secondTriSave").hide();
          $("#secondTriUpdate").show();
        } else if (res.status == 300) {
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        }
      },
    });
    });
    
    $(document).on("submit", "#thirdTrimesterInsert", function (e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var selectedValue = selectElement.value;
    
    formData.append("selected_row", selectedValue);
    formData.append("patient_id", patientID);
    formData.append("checkup", rightTab);
    
    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === 'thirdTriSave') {
      formData.append("third_trimesters_insert", true);
    } else if (clickedButton.id === 'thirdTriUpdate') {
      formData.append("third_trimesters_update", true);
    }
    
    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 200) {
          $("#thirdTriSave").hide();
          $("#thirdTriUpdate").show();
        } else if (res.status == 300) {
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        }
      },
    });
    });
    
    $(document).on("submit", "#birthExperienceInsert", function (e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var selectedValue = selectElement.value;
    
    formData.append("selected_row", selectedValue);
    formData.append("patient_id", patientID);
    
    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === 'birthExSave') {
      formData.append("birth_experience_insert", true);
    } else if (clickedButton.id === 'birthExUpdate') {
      formData.append("birth_experience_update", true);
    }
    
    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if(res.status == 200) {
          $("#birthExSave").hide();
          $("#birthExUpdate").show();
        } else if (res.status == 300) {
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        }
      },
    });
    });
    
    $(document).on("submit", "#patientsDetailsInsert", function (e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var selectedValue = selectElement.value;
    
    formData.append("selected_row", selectedValue);
    formData.append("patient_id", patientID);
    
    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === 'patientSave') {
      formData.append("patients_details_insert", true);
    } else if (clickedButton.id === 'patientUpdate') {
      formData.append("patients_details_update", true);
    }
    
    $.ajax({
      type: "POST",
      url: "server/prenatal_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if(res.status == 200) {
          $("#patientSave").addClass("d-none");
          $("#patientUpdate").removeClass("d-none");
          //$("#patientSave").hide();
          //$("#patientUpdate").show();
        } else if (res.status == 300) {
          $("#errorMessage").removeClass("d-none");
          $("#errorMessage").text(res.message);
        }
      },
    });
    });
    
    });
    
    $(document).on('click', '.viewPatient', function () {
        var rffrl_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "server/prenatal_function.php?view_patient_id=" + rffrl_id,
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
      var patients_id = $('.createNewPrenatalRecord').data('patient-id');
    
      alert(patients_id);
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