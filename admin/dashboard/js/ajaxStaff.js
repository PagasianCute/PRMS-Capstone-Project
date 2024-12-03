document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("addStaff");
  const input = document.getElementById("staffformFile");
  const imagePreview = document.getElementById("staffimagePreview");
  const imageName = document.getElementById("staffimage_name");

  var staffModal = $("#staffModal");

  tooltip();
  DefaultStaffModal();

  function tooltip() {
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  input.addEventListener("change", function () {
    // Get the selected file
    const file = input.files[0];

    // Check if a file is selected
    if (file) {
      // Check if the file type is an image
      if (!file.type.startsWith("image/")) {
        alert("Please select a valid image file (JPEG, PNG, GIF).");
        input.value = ""; // Clear the file input
        return;
      }

      // Read the file as a data URL
      const reader = new FileReader();

      reader.onload = function (e) {
        // Update the image preview with the data URL
        imagePreview.src = e.target.result;
      };

      reader.readAsDataURL(file);
    }
  });

  $("#staffuploadButton").on("click", function () {
    input.click();
  });

  $(document).on("click", "#staffsaveButton", function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    formData.append("add_staff", true);

    // Get the file input element
    const fileInput = document.getElementById("staffformFile");

    // Check if a file is selected
    if (fileInput.files.length > 0) {
      // Append the file to the FormData object
      formData.append("staffformFile", fileInput.files[0]);
    } else {
      // If no file is selected, show an error message and prevent form submission
      $("#errorMessage")
        .text("Please choose an image file.")
        .removeClass("d-none");
      return;
    }

    var requiredFields = [
      "#fname",
      "#lname",
      "#contactNum",
      "#address",
      "#role",
    ];
    var isValid = true;

    for (var i = 0; i < requiredFields.length; i++) {
      if ($(requiredFields[i]).val() === "") {
        isValid = false;
        break; // Exit the loop if an empty required field is found
      }
    }

    if (!isValid) {
      $("#errorMessage").text("Please fill all fields.").removeClass("d-none");
      return;
    }

   $.ajax({
  type: "POST",
  url: "server/staff_function.php",
  data: formData,
  processData: false,
  contentType: false,
  success: function (response) {
    var res = jQuery.parseJSON(response);
    if (res.status == 404) {
      console.log(res);
      alert("Error: " + res.message);
    } else if (res.status == 200) {
      // Close the modal upon successful submission
      staffModal.modal("hide");

      // Reload the staffTable
      $("#staffTable").load(location.href + " #staffTable");

      var imagePath = "../dashboard/assets/patient.png";
      $("#staffimagePreview").attr("src", imagePath);
      $("#addStaff")[0].reset();
      $("#errorMessage").addClass("d-none");
    }
  },
  error: function (xhr, status, error) {
    e.preventDefault(); // Prevent the default form submission behavior
    console.error("AJAX Error:", error);
    alert("Error: Something went wrong. Please try again.");
  },
});
});

  $(document).on("click", ".editStaff", function () {
    var staff_id = $(this).val();
    var $tooltipTrigger = $(this);
    var tooltipInstance = bootstrap.Tooltip.getInstance($tooltipTrigger[0]);

    if (tooltipInstance) {
      tooltipInstance.hide();
    }
    $.ajax({
      type: "GET",
      url: "server/staff_function.php?view_staff=" + staff_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 422) {
          console.log(res.message);
        } else if (res.status == 200) {
          $("#fname").val(res.data.fname);
          $("#mname").val(res.data.mname);
          $("#lname").val(res.data.lname);
          $("#username").val(res.data.username);
          $("#contactNum").val(res.data.contact_num);
          $("#address").val(res.data.address);
          $("#birth_date").val(res.data.birth_date);
          $("#role").val(res.data.role);
          var imagePath = "../../assets/" + res.data.img;
          $("#staffimagePreview").attr("src", imagePath);
          EditStaffModal();
          var staffModal = $("#staffModal");
          staffModal.modal("show");
          staffModal.on("hidden.bs.modal", function () {
            DefaultStaffModal();
            var imagePath = "../../assets/patient.png";
            $("#staffimagePreview").attr("src", imagePath);
            $("#addStaff")[0].reset();
          });
        }
      },
    });
  });

  $(document).on("click", ".deleteStaff", function () {
    var staff_id = $(this).val();
    var $tooltipTrigger = $(this); // Save the tooltip trigger element
    if (confirm("Are you sure you want to delete this staff?")) {
      $.ajax({
        type: "GET",
        url: "server/staff_function.php?delete_staff=" + staff_id,
        success: function (response) {
          var res = JSON.parse(response);
          if (res.status == 422) {
            alert(res.message);
          } else if (res.status == 200) {
            // Hide the tooltip
            var tooltipInstance = bootstrap.Tooltip.getInstance(
              $tooltipTrigger[0]
            );
            if (tooltipInstance) {
              tooltipInstance.hide();
            }
            // Update content
            $("#staffTable").load(location.href + " #staffTable", function () {
              tooltip(); // Call tooltip function after content is loaded
            });
          }
        },
      });
    }
  });

  $(document).on("click", "#staffupdateButton", function () {
    alert("Updated!");
  });

  function DefaultStaffModal() {
    $("#addStaff input").removeAttr("readonly");
    $("#staffeditButton").addClass("d-none");
    $("#staffupdateButton").addClass("d-none");
    $("#cancel").addClass("d-none");
    $("#staffsaveButton").removeClass("d-none");
  }

  function EditStaffModal() {
    $("#addStaff input").attr("readonly", true);
    $("#staffeditButton").removeClass("d-none");
    $("#staffupdateButton").removeClass("d-none");
    $("#cancel").addClass("d-none");
    $("#staffsaveButton").addClass("d-none");
    $("#staffupdateButton").addClass("d-none");
  }

  $("#staffeditButton").on("click", function () {
    $("#addStaff input").removeAttr("readonly");
    $("#staffupdateButton").removeClass("d-none");
    $("#staffeditButton").addClass("d-none");
    $("#cancel").removeClass("d-none");
    $("#staffsaveButton").addClass("d-none");
  });

  $("#cancel").on("click", function () {
    $("#addStaff input").attr("readonly", true);
    $("#staffeditButton").removeClass("d-none");
    $("#staffupdateButton").removeClass("d-none");
    $("#cancel").addClass("d-none");
    $("#staffsaveButton").addClass("d-none");
    $("#staffupdateButton").addClass("d-none");
    $("#addStaff")[0].reset();
  });
});
