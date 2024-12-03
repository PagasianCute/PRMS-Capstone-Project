<?php
session_start();
require __DIR__ . '../../../vendor/autoload.php';
require_once '../../db/db_conn.php';

if (!isset($_SESSION["adminaccount"])) {
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
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
      integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
      crossorigin=""
    />

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Include Bootstrap JS -->
    <script src="../../bootstrap_cdn/bootstrap.bundle.min.js"></script>

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
			<nav id="sidebar">
				<div class="p-4 pt-5">

                <a href="#" class="img logo rounded-circle mb-5" style="background-image: url(../../assets/administrative.jpg);"></a>
                <h3 class="facility"><?php echo $_SESSION["adminrole"]?></h3>
            <ul class="list-unstyled components mb-5">
                <li>
                    <a href="admin-index.php" class="sidebarbtn <?php echo basename($_SERVER['PHP_SELF']) === 'admin-index.php' ? 'active' : ''; ?>" id="home-link">
                        <i class="fi fi-sr-apps"></i></i><span class="sidebar-label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="admin-accounts.php" class="sidebarbtn <?php echo basename($_SERVER['PHP_SELF']) === 'admin-accounts.php' ? 'active' : ''; ?>" id="accounts-link">
                        <i class="fi fi-sr-user-pen"></i><span class="sidebar-label">Accounts</span>
                    </a>
                </li>
                <div class="footer">
              <li>
                  <a href="#" data-bs-toggle="modal"><i class="fi fi-ss-home"></i><span class="sidebar-label">Home Page</span></a>
              </li>
              </div>
            </ul>
          </div>
        </nav>


        <!-- Page Content  -->
      <div id="content">

      <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

        <button type="button" id="toggleButton" class="btn"><i class="fi fi-br-menu-burger"></i></button>
        
        <!-- Remove 'navbar-collapse' class for horizontal stacking -->
        <div class="nav-items">
            <ul class="navbar-nav flex-row"> <!-- Use 'flex-row' class to stack items horizontally -->
            <li class="nav-item">
                    <a href="#" class="notification"><i class="fi fi-rr-interrogation"></i></a>
                </li>
                <li class="nav-item">
                    <a href="#" class="notification"><i class="fi fi-ss-bell"></i></a>
                </li>
                  <?php
                    if (isset($_SESSION["adminaccount"])) {
                      // Second account is logged in
                      echo '<li class="nav-item">
                      <div class="dropdown">
                      <div class="logo-container">
                          <img src="../../assets/' . $_SESSION["adminimg"] . '" alt="Logo" class="logo-icon">
                      </div>
                      <div class="button-container">
                      <a href="#" role="button" class="user-name">' . $_SESSION["adminname"] . '<i class="fi fi-sr-angle-small-down"></i></a>
                      <p class="user-description">'. $_SESSION["adminrole"].'</p>
                    </div>
                  
                    <div class="dropdown-content">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#staffEditModal">Profile</a>
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
        <?php echo '<h1 class="modal-title" id="exampleModalLabel">' . $_SESSION["adminname"] . '</h1>';?>
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
                              if (isset($_SESSION["adminaccount"])) {
                                ?>
                            <div class="image-content">
                                <img src="../../assets/<?php echo $_SESSION["adminimg"] ?>" alt="Logo" class="profile-icon" id="imagePreview">
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
                              if (isset($_SESSION["adminaccount"])) {
                                ?>
                                <input type="text" hidden class="form-control" name="id" value="<?php echo $_SESSION["adminid"] ?>">
                                <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" aria-describedby="emailHelp" value="<?php echo $_SESSION["adminname"] ?>">
                                      </div>
                                      <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo $_SESSION["adminemail"] ?>">
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

    <script>
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

