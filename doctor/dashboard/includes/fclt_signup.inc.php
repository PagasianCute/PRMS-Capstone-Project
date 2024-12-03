<?php

 if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdrepeat"];
    $id = $_POST["id"];
    $address = $_POST["address"];
    $fclt = $_POST["facility"];

    require_once 'dbh.inc.php';
    require_once 'fclt_functions.inc.php';

    if(emptyInputSignup($name, $pwd, $pwdRepeat, $id,$address , $fclt) !== false){
        header("Location: ../fclt_signup.php?error=emptyinput");
        exit();
    }
    if(invalidfcltname($name) !== false){
        header("Location: ../fclt_signup.php?error=invalidfacilityname");
        exit();
    }
    if(pwdMatch($pwd, $pwdRepeat) !== false){
        header("Location: ../fclt_signup.php?error=passworddontmatch");
        exit();
    }
    if(invalidfclid($id) !== false){
        header("Location: ../fclt_signup.php?error=invalidemail");
        exit();
    }
    if(nameExist($conn, $name) !== false){
        header("Location: ../fclt_signup.php?error=facilitynameExist");
        exit();
    }
    if(idExist($conn, $id) !== false){
        header("Location: ../fclt_signup.php?error=facilityIdExist");
        exit();
    }
    if(selectFclt($fclt) !== false){
        header("Location: ../fclt_signup.php?error=selectfacility");
        exit();
    }

    createUser($conn, $name, $pwd, $id, $fclt, $address);

 }else{
    header("Location: ../fclt_signup.php");
 }