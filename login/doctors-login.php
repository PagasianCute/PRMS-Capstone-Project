<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../bootstrap_cdn/bootstrap.min.css">
    <script defer src="../bootstrap_cdn/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="css/admin-login.css">
    <title>PRMS</title>
</head>
<style>

</style>
<body>
    <div class="container">
    <h1>Doctor login</h1>
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-lg-6">
                <img src="../assets/doctor.jpg" class="img-fluid rounded-start rounded-end" alt="...">
                </div>
                <div class="col-lg-6">
                <div class="card-body form col-lg-12">
                    <h1 class="card-title">Login in your account</h1>
                    <form action="includes/login.inc.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Facility ID</label>
                        <input type="text" class="form-control" name="uid" id="uid">
                    </div>
                    <div class="mb-3">
                    <label for="pwd" class="form-label">Password</label>
                        <input type="password" class="form-control" name="pwd" id="pwd">
                    </div>
                    <button class="btn btn-primary" type="submit" name="submit">Login</button>
                    <a class="btn btn-outline-primary" href="../index.php" role="button">Home</a> 
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
