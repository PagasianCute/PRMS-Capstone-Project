<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Add the Bootstrap-datepicker CSS (You can replace this with the actual CDN link) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" integrity="sha384-...">

    <title>Hello, world!</title>
  </head>
  <body>
    <div class="form-group col-sm-12 col-md-6 col-lg-4">
        <label for="datepicker1">asdd</label>
        <div class="input-group">
            <input type="text" id="" class="form-control datepicker">
        </div>
    </div>

    <div class="form-group col-sm-12 col-md-6 col-lg-4">
        <label for="datepicker2">asdd</label>
        <div class="input-group">
            <input type="text" id="" class="form-control datepicker">
        </div>
    </div>

    <!-- Include jQuery (required for Bootstrap datepicker) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Bootstrap JS (required for Bootstrap datepicker) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Include Bootstrap-datepicker JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js" integrity="sha384-..."></script>

    <script>
    $(document).ready(function () {
        // Initialize all datepicker inputs with the 'datepicker' class
        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true
        });
    });
    </script>
  </body>
</html>
