<?php
include_once 'header.php';
$fclt_type = $_SESSION["fclttype"];
?>

<div class="container">
  <h1 class="label">Welcome back <?php echo $_SESSION["usersname"] ?></h1>

<?php if($fclt_type == 'Birthing Home'){?>
<div class="row d-flex justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    <div class="col">
      <div class="card">
          <img src="assets/referral.jpg" class="card-img-top" alt="...">
          <div class="card-body">
              <h5 class="card-title">Referral</h5>
              <p class="card-text">Referral system interface here</p>
              <a href="facility/dashboard/index.php" class="btn btn-primary">Visit</a>
          </div>
      </div>
    </div>
  <div class="col">
    <div class="card">
        <img src="assets/newadministrative.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Administrative</h5>
            <p class="card-text">Administrative interface here</p>
            <a href="admin/dashboard/admin-referrals.php" class="btn btn-primary">Visit</a>
        </div>
    </div>
  </div>
  <?php if($users_role == 'Nurse' || $users_role == 'Nurse' || $users_role == 'Midwife' || $users_role == 'Doctor'){?>
  <div class="col">
    <div class="card">
        <img src="assets/prenatal.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Prenatal</h5>
            <p class="card-text">Prenatal interface here</p>
            <a href="prenatal/dashboard/prenatal-index.php" class="btn btn-primary">Visit</a>
        </div>
    </div>
  </div>
  <?php } ?>
</div>
<?php } ?>

<?php if($fclt_type == 'Hospital'){?>
<div class="row d-flex justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    <div class="col">
      <div class="card">
          <img src="assets/referral.jpg" class="card-img-top" alt="...">
          <div class="card-body">
              <h5 class="card-title">Referral</h5>
              <p class="card-text">Referral system interface here</p>
              <a href="facility/dashboard/index.php" class="btn btn-primary">Visit</a>
          </div>
      </div>
    </div>
  <?php if($users_role == 'Doctor'){?>
  <div class="col">
    <div class="card">
        <img src="assets/doctors.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Doctors</h5>
            <p class="card-text">Doctor interface here</p>
            <a href="doctor/dashboard/index.php" class="btn btn-primary">Visit</a>
        </div>
    </div>
  </div>
  <?php } ?>
  <div class="col">
    <div class="card">
        <img src="assets/newadministrative.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Administrative</h5>
            <p class="card-text">Administrative interface here</p>
            <a href="admin/dashboard/admin-referrals.php" class="btn btn-primary">Visit</a>
        </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if($fclt_type == 'Provincial Hospital'){?>
<div class="row d-flex justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    <div class="col">
      <div class="card">
          <img src="assets/referral.jpg" class="card-img-top" alt="...">
          <div class="card-body">
              <h5 class="card-title">Referral</h5>
              <p class="card-text">Referral system interface here</p>
              <a href="facility/dashboard/index.php" class="btn btn-primary">Visit</a>
          </div>
      </div>
    </div>
  <?php if($users_role == 'Doctor'){?>
  <div class="col">
    <div class="card">
        <img src="assets/doctors.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Doctors</h5>
            <p class="card-text">Doctor interface here</p>
            <a href="doctor/dashboard/index.php" class="btn btn-primary">Visit</a>
        </div>
    </div>
  </div>
  <?php } ?>
  <div class="col">
    <div class="card">
        <img src="assets/newadministrative.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Administrative</h5>
            <p class="card-text">Administrative interface here</p>
            <a href="admin/dashboard/admin-referrals.php" class="btn btn-primary">Visit</a>
        </div>
    </div>
  </div>
</div>
<?php } ?>

</div>

<?php
include_once 'footer.php';
?>