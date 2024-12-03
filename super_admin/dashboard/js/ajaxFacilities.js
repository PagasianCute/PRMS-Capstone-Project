document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("addStaff");
  const input = document.getElementById("staffformFile");
  const imagePreview = document.getElementById("staffimagePreview");
  const imageName = document.getElementById("staffimage_name");

  var staffModal = $("#staffModal");

  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  tooltip();

  function tooltip() {
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  input.addEventListener("change", function (e) {
    e.preventDefault();
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

  $("#staffuploadButton").on("click", function (e) {
    e.preventDefault();
    input.click();
  });

  $(document).on("submit", "#addStaff", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    var clickedButton = e.originalEvent.submitter;
    if (clickedButton.id === "staffsaveButton") {
      formData.append("add_facility", true);
    } else if (clickedButton.id === "staffupdateButton") {
      formData.append("update_facility", true);
    }

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

    $.ajax({
      type: "POST",
      url: "server/facilities_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log("Raw response:", response); // Add this to inspect the response
        var res = jQuery.parseJSON(response); // Attempt to parse the response
        if (res.status == 404) {
            console.log(res);
            alert("Error: " + res.message);
        } else if (res.status == 200) {
            staffModal.modal("hide");
            $("#facilityTable").load(location.href + " #facilityTable");
            var imagePath = "../dashboard/assets/patient.png";
            $("#staffimagePreview").attr("src", imagePath);
            $("#addStaff")[0].reset();
            $("#errorMessage").addClass("d-none");
        }
    },
    
    });
  });

  $(document).on("submit", "#verificationForm", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("facility_verification", true);

    $.ajax({
      type: "POST",
      url: "server/facilities_function.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        if (res.status == 404) {
          console.log(res);
          alert("Error: " + res.message);
        } else if (res.status == 200) {
          $("#verifyModal").modal("hide");
          $("#facilityTable").load(location.href + " #facilityTable");
          $(".main-cards").load(location.href + " .main-cards");
        }
      },
    });
  });

  $(document).on("click", ".deleteFacility", function () {
    var fclt_id = $(this).val();
    var $tooltipTrigger = $(this); // Save the tooltip trigger element
    if (confirm("Are you sure you want to delete this patient?")) {
      $.ajax({
        type: "GET",
        url: "server/facilities_function.php?remove_fclt=" + fclt_id,
        success: function (response) {
          var res = jQuery.parseJSON(response);
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
            $("#facilityTable").load(
              location.href + " #facilityTable",
              function () {
                tooltip(); // Call tooltip function after content is loaded
              }
            );
          }
        },
      });
    }
  });

  $(document).on("click", ".editFacility", function () {
    var fclt_id = $(this).val();
    var $tooltipTrigger = $(this);
    var tooltipInstance = bootstrap.Tooltip.getInstance($tooltipTrigger[0]);

    if (tooltipInstance) {
      tooltipInstance.hide();
    }
    EditStaffModal();

    $.ajax({
      type: "GET",
      url: "server/facilities_function.php?view_fclt=" + fclt_id,
      success: function (response) {
        var res = jQuery.parseJSON(response);
        console.log(res);
        if (res.status == 422) {
          console.log(res);
        } else if (res.status == 200) {
          $('#add_fclt_id').val(res.data.fclt_id);
          $("#fclt_ref_id").val(res.data.fclt_ref_id);
          $("#fclt_name").val(res.data.fclt_name);
          $("#fclt_type").val(res.data.fclt_type);
          $("#fclt_contact").val(res.data.fclt_contact);
          $("#region-select").val(res.data.region_code).change();
          $("#province-select").val(res.data.province).change();
          $("#municipality-select").val(res.data.municipality).change();
          var imagePath = "../../assets/" + res.data.img_url;
          $("#staffimagePreview").attr("src", imagePath);
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
      error: function (xhr, status, error) {
        console.error("XHR Response:", xhr.responseText);
        console.error("Status:", status);
        console.error("Error:", error);
    }
    });
  });

  function DefaultStaffModal() {
    $("#addStaff input").removeAttr("readonly");
    $("#staffeditButton").addClass("d-none");
    $("#staffupdateButton").addClass("d-none");
    $("#cancel").addClass("d-none");
    $("#staffsaveButton").removeClass("d-none");
  }

  function EditStaffModal() {
    $("#staffeditButton").removeClass("d-none");
    $("#staffupdateButton").removeClass("d-none");
    $("#staffsaveButton").addClass("d-none");
  }

  $(document).on("click", ".verifyFacility", function () {
    var fclt_id = $(this).val();
    var $tooltipTrigger = $(this);
    var tooltipInstance = bootstrap.Tooltip.getInstance($tooltipTrigger[0]);

    if (tooltipInstance) {
      tooltipInstance.hide();
    }

    $.ajax({
      type: "GET",
      url: "server/facilities_function.php?view_fclt=" + fclt_id,
      dataType: "json", // Specify data type as JSON
      success: function (res) {
        if (res.status == 422) {
          console.log(res.message);
        } else if (res.status == 200) {
          $("#verifyModal").modal("show");
          $("#address").val(res.data.municipality);
          $("#fclt_id").val(res.data.fclt_id);
          geocodeAddress(res.data.municipality);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error in Ajax request:", error);
      },
    });
  });

  // Initialize the modal
  const mapModal = new bootstrap.Modal(document.getElementById("verifyModal"));
  let map;
  let marker;

  // Function to create the map
  function createMap() {
    // Check if the map instance already exists
    if (map) {
      return;
    }

    // Create a new map instance
    map = L.map("map").setView([12.8797, 121.774], 6);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution: "© OpenStreetMap",
    }).addTo(map);
  }

  // Function to handle the geocode address
  function geocodeAddress(address) {
    getCoordinates(address)
      .then((coordinates) => {
        displayCoordinates(coordinates);

        if (marker) {
          map.removeLayer(marker);
        }

        marker = L.marker([coordinates.latitude, coordinates.longitude]).addTo(
          map
        );
        map.setView([coordinates.latitude, coordinates.longitude], 15);

        // Show the modal
        mapModal.show();
      })
      .catch((error) => {
        console.error("Error geocoding address:", error.message);
      });
  }

  // Function to get coordinates
  async function getCoordinates(address) {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
        address
      )}&countrycodes=PH&bounded=1&viewbox=116.93,4.6,126.6,21.2`
    );
    const data = await response.json();

    if (data.length > 0) {
      return {
        latitude: parseFloat(data[0].lat),
        longitude: parseFloat(data[0].lon),
      };
    } else {
      throw new Error("Invalid address or address not in the Philippines");
    }
  }

  // Function to display coordinates
  function displayCoordinates(coordinates) {
    $("#latitude").val(coordinates.latitude);
    $("#longitude").val(coordinates.longitude);
  }

  // Initialize the map when the modal is shown
  $("#verifyModal").on("shown.bs.modal", function () {
    createMap();
  });
});

$(document).ready(function () {
  var regionSelect = $("#region-select");
  var provinceSelect = $("#province-select");
  var municipalitySelect = $("#municipality-select");

  $.getJSON(
    "philippine_provinces_cities_municipalities_and_barangays_2019v2.json",
    function (data) {
      // Populate the region dropdown
      $.each(data, function (regionCode, regionInfo) {
        regionSelect.append(
          `<option value="${regionCode}">${regionInfo.region_name}</option>`
        );
      });

      // Handle region selection change
      regionSelect.change(function () {
        var selectedRegionCode = $(this).val();

        if (selectedRegionCode) {
          // Enable the province dropdown
          provinceSelect.prop("disabled", false);

          // Clear and populate the province dropdown based on the selected region
          provinceSelect
            .empty()
            .append(
              "<option selected disabled value>Select a Province</option>"
            );
          $.each(
            data[selectedRegionCode].province_list,
            function (provinceName) {
              provinceSelect.append(
                `<option value="${provinceName}">${provinceName}</option>`
              );
            }
          );

          // Set the region name in the input field
          var selectedRegionInfo = data[selectedRegionCode];
          $("#region-name").val(selectedRegionInfo.region_name);
        } else {
          // If no region is selected, disable and clear the province dropdown
          provinceSelect.prop("disabled", true).empty();
          $("#region-name").val(""); // Clear the region name input field
        }

        // Clear and disable the municipality and barangay dropdowns
        municipalitySelect
          .empty()
          .prop("disabled", true)
          .append(
            "<option selected disabled value>Select a Municipality</option>"
          );
      });

      // Handle province selection change
      provinceSelect.change(function () {
        var selectedProvince = $(this).val();

        if (selectedProvince) {
          // Enable the municipality dropdown
          municipalitySelect.prop("disabled", false);

          // Clear and populate the municipality dropdown based on the selected province
          municipalitySelect
            .empty()
            .append(
              "<option selected disabled value>Select a Municipality</option>"
            );
          $.each(
            data[regionSelect.val()].province_list[selectedProvince]
              .municipality_list,
            function (municipalityName) {
              municipalitySelect.append(
                `<option value="${municipalityName}">${municipalityName}</option>`
              );
            }
          );
        } else {
          // If no province is selected, disable and clear the municipality dropdown
          municipalitySelect.prop("disabled", true).empty();
        }
      });
    }
  );
});
