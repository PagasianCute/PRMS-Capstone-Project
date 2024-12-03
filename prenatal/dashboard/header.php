<?php
session_start();
$admin_id = $_SESSION['usersid'];
$fclt_id = $_SESSION['fcltid'];
require_once '../../db/db_conn.php';

if (!isset($_SESSION["second_account"])) {
  header("Location: ../../login/admin-login.php"); // Redirect to the login page for the first account
  exit();
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Referral System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../bootstrap_cdn/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Include Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>

    <!-- Google Font Link for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Include Date Format -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-dateFormat/1.0/jquery.dateFormat.min.js"></script>

    <!-- Include Bootstrap JS -->
    <script src="../../bootstrap_cdn/bootstrap.bundle.min.js"></script>

    <script src="js/toasts.js"></script>

    <script>
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        if (isMobile) {
            console.log("Mobile device detected");
            var mobile = true;

            // Reduce the viewport scale for mobile devices
            document.querySelector('meta[name="viewport"]').setAttribute('content', 'width=device-width, initial-scale=0.8');
        } else {
            console.log("Desktop device detected");
            var mobile = false;
        }
    </script>
</head>
  <body>
  <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
			<nav id="sidebar" class="active">
            <img class="facility-logo" src="../../assets/<?php echo $_SESSION["fcltimg"] ?>" alt="facility logo">
                <div class="facility-header">
                  <h3 class="text"><?php echo $_SESSION["fcltname"] ?><img id="imageTooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="This Facility is Verified" class="verification-icon" src="../../assets/Verified.png" alt="facility logo"></h3>
                </div>
              <ul class="list-unstyled components mb-5">
                  <li>
                    <a href="prenatal-index.php" class="sidebarbtn <?php echo basename($_SERVER['PHP_SELF']) === 'prenatal-index.php' ? 'active' : ''; ?>" id="home-link" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Dashboard">
                    <i class="fi fi-sr-apps"></i></i><span class="sidebar-label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="prenatal.php" class="sidebarbtn <?php echo (basename($_SERVER['PHP_SELF']) === 'prenatal.php' || basename($_SERVER['PHP_SELF']) === 'add_prenatal.php' || basename($_SERVER['PHP_SELF']) === 'view_prenatal.php') ? 'active' : ''; ?>" id="prenatal-link" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Prenatal">
                    <i class="fi fi-sr-hospital-user"></i><span class="sidebar-label">Your Patients</span>
                    </a>
                </li>
                <li>
                    <a href="patients.php" class="sidebarbtn <?php echo (basename($_SERVER['PHP_SELF']) === 'patients.php' || basename($_SERVER['PHP_SELF']) === 'view_patient.php') ? 'active' : ''; ?>" id="prenatal-link" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Patients">
                        <i class="fi fi-ss-users"></i><span class="sidebar-label">All Patients</span>
                    </a>
                </li>
                <?php 
              if ($_SESSION["fclttype"] == 'Birthing Home') {
               ?>
                <li>
                    <a href="inventory.php" class="sidebarbtn <?php echo (basename($_SERVER['PHP_SELF']) === 'inventory.php') ? 'active' : ''; ?>" id="inventory-link" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Inventory">
                    <i class="fi fi-sr-cloud-download-alt"></i><span class="sidebar-label">Birth Booklet</span>
                    </a>
                </li>
                <?php 
               }
              ?>
                <div class="footer">
              <li>
                  <a href="../../index.php" data-bs-toggle="modal"><i class="fi fi-ss-home"></i><span class="sidebar-label">Home Page</span></a>
              </li>
              </div>
            </ul>
        </nav>


        <!-- Page Content  -->
      <div id="content">

      <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

        <div class="page-header">
            <button type="button" id="toggleButton" class="btn"><i class="fi fi-br-menu-burger"></i></button>
            <h2>Prenatal</h2>
        </div>
        
        <!-- Remove 'navbar-collapse' class for horizontal stacking -->
        <div class="nav-items">
            <ul class="navbar-nav flex-row"> <!-- Use 'flex-row' class to stack items horizontally -->
            <li class="nav-item">
                    <a href="#" class="notification"><i class="fi fi-rr-interrogation"></i></a>
                </li>
                <li class="nav-item">
                    <a href="#" class="notification notification-trigger"><i class="fi fi-ss-bell"></i></a>
                </li>
                <div class="notification-content shadow">
                  <div class="notification-header">
                    Notification
                  </div>
                  <div class="notifications-container">
                    <div class="notifications">
                      <!-- Notifications will be appended here -->
                    </div>
                  </div>
                  <div class="footer">
                    <button id="markAllAsReadBtn"><i class="fi fi-rr-check-double"></i>Mark all as Read</button>
                  </div>
                </div>
                  <?php
                    if (isset($_SESSION["second_account"])) {
                      // Second account is logged in
                      echo '<li class="nav-item">
                      <div class="dropdown">
                      <div class="logo-container">
                          <img src="../../assets/'. $_SESSION["usersimg"] .'" alt="Logo" class="logo-icon">
                      </div>
                      <div class="button-container">
                      <a href="#" role="button" class="user-name">' . $_SESSION["usersname"] . '<i class="fi fi-sr-angle-small-down"></i></a>
                      <p class="user-description">'. $_SESSION["usersrole"].'</p>
                    </div>
                  
                    <div class="dropdown-content">
                        <a href="../../includes/logout.inc.php">Logout</a>
                    </div>
                </div>
                </li>';
                  } else {
                      // No account is logged in, display login links
                      echo '<li class="nav-item">
                      <a href="#" data-bs-toggle="modal" class="user-login" data-bs-target="#loginModal">Login</a>
                        </li>';
                  }
                  ?>
                
              </ul>
      
          </div>
        </nav>

        <!-- Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <?php echo '<h1 class="modal-title" id="exampleModalLabel">' . $_SESSION["fcltname"] . '</h1>';?>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-theme="custom"></button>
      </div>
      <div class="modal-body">
        <h5>Are you sure you want to logout?</h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn close" data-bs-dismiss="modal">Cancel</button>
        <a class="btn btn-primary" href="includes/fclt_logout.inc.php" role="button">Logout</a>
      </div>
    </div>
  </div>
</div>

    <!-- EDIT STAFF PROFILE modal -->
    <div class="modal fade" id="staffEditModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" id="staffEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h1>Staff Profile</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="status"></div>
                    <form id="user_profile" enctype="multipart/form-data">
                        <div class="image-profile">
                        <?php
                              if (isset($_SESSION["second_account"])) {
                                ?>
                            <div class="image-content">
                                <img src="../../assets/<?php echo $_SESSION["usersimg"] ?>" alt="Logo" class="profile-icon" id="imagePreview">
                            </div>
                            <?php
                              }
                              ?>
                            <div class="edit-button">
                                <!-- Use a button to trigger the file input -->
                                <button type="button" class="btn btn-primary" id="uploadButton">Upload Image</button>

                                <!-- Hide the file input element -->
                                <input style="display: none;" type="file" id="formFile" name="profile_image" onchange="displaySelectedImage()">

                            </div>
                            <div id="image_name"></div>
                        </div>
                        <div class="profile-details">
                            <!-- Additional form fields for name and description -->
                            <?php
                              if (isset($_SESSION["second_account"])) {
                                ?>
                                <input type="text" hidden class="form-control" name="id" value="<?php echo $_SESSION["usersid"] ?>">
                                <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" aria-describedby="emailHelp" value="<?php echo $_SESSION["usersname"] ?>">
                                      </div>
                                      <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo $_SESSION["email"] ?>">
                                      </div>
                                      <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" placeholder="*****">
                                      </div>
                                <?php
                              }
                              ?>
                            <button type="button" class="btn btn-primary" id="saveButton">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>

    <script>
    $(document).ready(function () {
        // Toggle notification content on button click
        $('.notification-trigger').click(function (e) {
            e.stopPropagation(); // Prevent the click event from propagating to the document
            $('.notification-content').toggle();
        });

        // Hide notification content when clicking outside of it
        $(document).click(function (e) {
            if (!$(e.target).closest('.notification-content').length && !$(e.target).is('.notification-trigger')) {
                $('.notification-content').hide();
            }
        });
    });

  var imageTooltip = document.getElementById('imageTooltip');

  var tooltip = new bootstrap.Tooltip(imageTooltip, {
    placement: 'top',
    trigger: 'hover focus', // Show on hover and focus
  });
        function displaySelectedImage() {
          const input = document.getElementById('formFile');
          const imagePreview = document.getElementById('imagePreview');
          const imageName = document.getElementById('image_name');

          if (input.files && input.files[0]) {
              const reader = new FileReader();

              reader.onload = function (e) {
                  imagePreview.src = e.target.result;
                  imageName.textContent = input.files[0].name;
              };

              reader.readAsDataURL(input.files[0]);
          }
      }

      // Add an event listener to the button to trigger the file input
      document.getElementById('uploadButton').addEventListener('click', function () {
          document.getElementById('formFile').click(); // Trigger the file input
      });

      document.getElementById('saveButton').addEventListener('click', function () {
          const formData = new FormData(document.getElementById('user_profile'));

          fetch('upload.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              document.getElementById('status').textContent = data.message;
              if (data.message === 'File uploaded successfully') {
                  // Close the modal if the upload is successful
                  const staffEditModal = new bootstrap.Modal(document.getElementById('staffEditModal'));
                  staffEditModal.hide();
              }
          })
          .catch(error => {
              console.error('Error:', error);
          });
      });
    </script>

