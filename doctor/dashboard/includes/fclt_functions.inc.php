<?php

function emptyInputSignup($name, $id, $pwd, $pwdRepeat, $fclt, $address){
    $result;
    if(empty($name) || empty($id) || empty($pwd) || empty($pwdRepeat) || empty($fclt) || empty($address)){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function invalidfcltname($name) {
    $result;
    if (!preg_match("/^[a-zA-Z0-9 ]*$/", $name)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidfclid($id){
    $result;
    if(!preg_match("/^[a-zA-Z0-9]*$/", $id)){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function pwdMatch($pwd, $pwdRepeat){
    $result;
    if($pwd !== $pwdRepeat){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function selectFclt($fclt){
    $result;
    if($fclt == "Select Facility"){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function nameExist($conn, $name){
    $sql = "SELECT * FROM facilities WHERE fclt_name = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../fclt_signup.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }else{
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function idExist($conn, $id){
    $sql = "SELECT * FROM facilities WHERE fclt_ref_id = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../fclt_signup.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }else{
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function  createUser($conn, $name, $pwd, $id, $fclt, $address){
    $sql = "INSERT INTO facilities (fclt_name, fclt_password, fclt_ref_id, fclt_type, fclt_address) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../fclt_signup.php?error=stmtfailed");
        exit();
    }

    $hasshedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "sssss", $name, $hasshedPwd, $id, $fclt, $address);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: ../fclt_signup.php?error=none");
    exit();
}


function emptyInputLogin($name, $pwd){
    $result;
    if(empty($name) || empty($pwd)){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function loginFacility($conn, $name, $pwd){
    $id = idExist($conn, $name, $name);

    if($id === false){
        header("Location: ../fclt_login.php?error=wronglogin");
        exit();
    }

    $pwdHashed = $id["fclt_password"];
    $checkPwd = password_verify($pwd, $pwdHashed);

    if($checkPwd === false){
        header("Location: ../fclt_login.php?error=wronglogin");
        exit();
    }else if($checkPwd === true){
        session_start();
        $_SESSION["first_account"] = true; // Set a session variable to indicate the first account is logged in
        $_SESSION["id"] = $id["fclt_id"];
        $_SESSION["names"] = $id["fclt_name"];
        $_SESSION["facility"] = $id["fclt_type"];
        header("Location: ../index.php?yown");
        exit();
    }
}