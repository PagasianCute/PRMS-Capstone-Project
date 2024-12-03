<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8" />
  <title>Dynamic Calendar JavaScript | CodingNepal</title>
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="../../bootstrap_cdn/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/calendar_style.css?v=<?php echo time(); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Google Font Link for Icons -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="../../bootstrap_cdn/bootstrap.bundle.min.js"></script>
  <script src="js/calendar_script.js" defer></script>
</head>

<body>
  <div class="wrapper">
    <div class="bg-light p-3">
      <p class="current-date"></p>
      <div class="icons">
        <span id="prev" class="material-symbols-rounded">chevron_left</span>
        <span id="next" class="material-symbols-rounded">chevron_right</span>
      </div>
    </div>
    <div class="calendar p-3">
      <ul class="weeks list-unstyled d-flex">
        <li class="flex-fill">Sun</li>
        <li class="flex-fill">Mon</li>
        <li class="flex-fill">Tue</li>
        <li class="flex-fill">Wed</li>
        <li class="flex-fill">Thu</li>
        <li class="flex-fill">Fri</li>
        <li class="flex-fill">Sat</li>
      </ul>
      <ul class="days list-unstyled"></ul>
    </div>
  </div>
</body>

</html>
