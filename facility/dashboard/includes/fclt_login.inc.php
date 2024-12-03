<?php

if(isset($_POST["submit"])){

    $name = $_POST["name"];
    $pwd = $_POST["pwd"];

    require_once 'dbh.inc.php';
    require_once 'fclt_functions.inc.php';

    if(emptyInputLogin($name, $pwd) !== false){
        header("Location: ../fclt_login.php?error=emptyinput");
        exit();
    }
    

    loginFacility($conn, $name, $pwd);
}else{
    header("Location: ../../login/fclt-login.php");
    exit();
}