<?php

 if(isset($_POST["submit"])){
    $fname = $_POST["fname"];
    $mname = $_POST["mname"];
    $lname = $_POST["lname"];
    $age = $_POST['age'];
    $sex = $_POST['sex'];


    require_once 'dbh.inc.php';
    require_once 'patients_functions.inc.php';

  /*  if(emptyInputSignup($fname, $mname, $lname, $sex) !== false){
        header("Location: ../prenatal.php?error=emptyinput");
        exit();
    }
    if(invalidfcltname($name) !== false){
        header("Location: ../fclt_signup.php?error=invaliduid");
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
*/
    createPatient($conn, $fname, $mname, $lname, $age, $sex);

 }else{
    header("Location: ../prenatal.php");
 }
