document.addEventListener("DOMContentLoaded", function() {

    var channel = pusher.subscribe(fclt_id);
    console.log('User ID ' + fclt_id);
    channel.bind('referral', function(data) {
        console.log('Received referral from: ' + data);
        $(".newReferrals").load(location.href + " .newReferrals");
    });

    $(document).on('click', '.viewRecord', function () {
        var rffrl_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "server/new_referrals_function.php?rffrl_id=" + rffrl_id,
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if (res.status == 422) {
                    alert(res.message);
                } else if (res.status == 200) {
                    $('#fclt_name').text(res.data.fclt_name);
                    $('#fclt_id').val(res.data.fclt_id);
                    $('#rffrl_id').val(res.data.rfrrl_id);
                    $('#name').val(res.data.name);
                    $('#age').val(res.data.age);
                    $('.referral-reason').addClass("d-none");
                    $('#cancel_button').addClass("d-none");
                    $('#decline_button').addClass("d-none");
                    $('#referralModal').modal('show');

                    $(document).on('click', '#decline_referral', function () {
                        $('.referral-reason').removeClass("d-none");
                        $('#decline_referral').addClass("d-none");
                        $('#accept_button').addClass("d-none");
                        $('#decline_button').removeClass("d-none");
                        $('#cancel_button').removeClass("d-none");
                    });

                    $(document).on('click', '#cancel_button', function () {
                        $('.referral-reason').addClass("d-none");
                        $('#cancel_button').addClass("d-none");
                        $('#decline_button').addClass("d-none");
                        $('#decline_referral').removeClass("d-none");
                        $('#accept_button').removeClass("d-none");
                    });

                    const referralModal = document.getElementById('referralModal')
                    referralModal.addEventListener('hidden.bs.modal', event => {
                        $('.referral-reason').addClass("d-none");
                        $('#cancel_button').addClass("d-none");
                        $('#decline_button').addClass("d-none");
                        $('#decline_referral').removeClass("d-none");
                        $('#accept_button').removeClass("d-none");
                    });
                }
            }
        });
    });

    $(document).on("click", "#decline_button", function () {
        var formData = new FormData($("#referral_form")[0]);
        formData.append("decline_referral", true);

        $.ajax({
            type: "POST",
            url: "new_function.php",
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
                    $('#reason').val('');
                }else if(res.status == 422){
                $("#errorMessage").removeClass("d-none");
                $("#errorMessage").text(res.message);
                }
            },
            complete: function () {
                $(".decline-loading").addClass("d-none");
                $("#decline_button").removeClass("disabled");
                $(".decline-span").removeClass("d-none");
            }
        });
    });

    $(document).on("click", "#accept_button", function () {
        var formData = new FormData($("#referral_form")[0]);
        formData.append("accept_referral", true);

        $.ajax({
            type: "POST",
            url: "server/new_function.php",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".accept-loading").removeClass("d-none");
                $("#accept_button").addClass("disabled");
                $(".accept-span").addClass("d-none");
            },
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if (res.status == 200) {
                    $("#referralModal").modal("hide");
                    $(".newReferrals").load(location.href + " .newReferrals");
                    $(".newArrivals").load(location.href + " .newArrivals");
                    $('#reason').val('');
                }
            },
            complete: function () {
                $(".accept-loading").addClass("d-none");
                $("#accept_button").removeClass("disabled");
                $(".accept-span").removeClass("d-none");
            }
        });
    });

    $(document).on("click", ".patientArrived", function () {
        var rffrl_id = $(this).val();
    
        $.ajax({
            type: "POST",
            url: "server/new_function.php",
            data: { 'patient_arrived': true, 'rffrl_id': rffrl_id }, // Properly structure the data
            dataType: 'json', // Specify the expected data type
            beforeSend: function () {
                $(".arrived-loading").removeClass("d-none");
                $(".patientArrived").addClass("disabled");
                $(".arrived-span").addClass("d-none");
            },
            success: function (response) {
                if (response.status == 200) {
                    $("#referralModal").modal("hide");
                    $(".newReferrals").load(location.href + " .newReferrals");
                    $(".newArrivals").load(location.href + " .newArrivals");
                    $('#reason').val('');
                }
            },
            complete: function () {
                $(".arrived-loading").addClass("d-none");
                $("#patientArrived").removeClass("disabled");
                $(".arrived-span").removeClass("d-none");
            }
        });
    });    

});