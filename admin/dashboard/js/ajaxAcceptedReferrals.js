document.addEventListener("DOMContentLoaded", () => {


    $(document).on('click', '.viewRecord', function () {
        var rffrl_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "new_function.php?rffrl_id=" + rffrl_id,
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if (res.status == 422) {
                    alert(res.message);
                } else if (res.status == 200) {
                    $('#fclt_name').text(res.data.fclt_name);
                    $('#rffrl_id').val(res.data.rfrrl_id);
                    $('#referralModal').modal('show');
    
                    var columnNames = res.column_data;
    
                    for (var i = 0; i < columnNames.length; i++) {
                        var columnName = columnNames[i];
                        var columnData = res.data[columnName];
                        $('#' + columnName).val(columnData);
                    }
    
                    // Display referral transactions
                    var querytransactions_data = res.transactions;
                    var referralTransactionsDiv = $('#referral_transactions');
                    var audit = document.querySelector(".referral-audit");
                    referralTransactionsDiv.empty(); // Clear any previous data
    
                    for (var i = 0; i < querytransactions_data.length; i++) {
                        var transactionData = querytransactions_data[i];
                        var status = transactionData.status;
                        var time = transactionData.time;
                        var fclt_name = transactionData.fclt_name;
    
                        if (status) {
                            audit.classList.remove("d-none");
                            var pElement = $('<p></p>'); // Create a new <p> element
                            pElement.text(status +" by "+ fclt_name +" at "+ time); // Include the label
                            referralTransactionsDiv.append(pElement); // Append the <p> element to the div
                        }
                    }
                }
            }
        });
    });
});