<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../bootstrap_cdn/bootstrap.min.css">
    <script defer src="../bootstrap_cdn/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="css/admin-login.css?v=<?php echo time(); ?>">
    <title>PRMS</title>
</head>
<body>
    <div class="container">
    <h1>Facility login</h1>
        <div class="card mb-3 col-md-9 col-sm-12">
            <div class="row g-0">
                <div class="col-lg-6">
                <div class="card-body form col-lg-12">
                    <h1 class="card-title">Create new account</h1>
                    <form action="includes/signup.inc.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                    <div class="mb-3">
                    <label for="pwd" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" id="email">
                    </div>
                    <div class="mb-3">
                    <label for="pwd" class="form-label">Username</label>
                        <input type="text" class="form-control" name="uid" id="uid">
                    </div>
                    <div class="mb-3">
                    <label for="pwd" class="form-label">Password</label>
                        <input type="password" class="form-control" name="pwd" id="pwd">
                    </div>
                    <div class="mb-3">
                    <label for="pwd" class="form-label">Repeat Password</label>
                        <input type="password" class="form-control" name="pwdrepeat" id="pwdrepeat">
                    </div>
                    <button class="btn btn-primary" type="submit" name="submit">Create</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
