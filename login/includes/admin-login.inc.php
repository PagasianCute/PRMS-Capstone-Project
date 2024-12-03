<?php

if (isset($_POST["submit"])) {
    
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];

    include "../class/dbh.class.php";
    include "../class/admin-login.class.php";
    include "../class/admin-login-contr.class.php";
    $login = new LoginContr($uid, $pwd);

    $login->loginUser();

    header("Location: ../admin-login.php?error=none");
    exit();
}