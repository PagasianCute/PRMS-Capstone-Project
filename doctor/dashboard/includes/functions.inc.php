<?php

function emptyInputSignup($name, $email, $username, $pwd, $pwdRepeat){
    $result;
    if(empty($name) || empty($email) || empty($username) || empty($pwd)|| empty($pwdRepeat)){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function invalidUid($username){
    $result;
    if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function invalidEmail($email){
    $result;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
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

function uidExist($conn, $username, $email){
    $sql = "SELECT * FROM users WHERE usersUid = ? OR usersEmail = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../signup.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
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

function createUser($conn, $name, $email, $username, $pwd){
    $sql = "INSERT INTO users (usersName, usersEmail, usersUid, usersPwd) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../signup.php?error=stmtfailed");
        exit();
    }

    $hasshedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $username, $hasshedPwd);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: ../signup.php?error=none");
    exit();
}

function emptyInputLogin($username, $pwd){
    $result;
    if(empty($username) || empty($pwd)){
        $result = true;
    }else{
        $result = false;
    }return $result;
}

function loginUser($conn, $username, $password){
    session_start(); // Start the session

    // Validate user input (e.g., check for empty fields)
    if (empty($username) || empty($password)) {
        header("Location: ../login.php?error=emptyfields");
        exit();
    }

    $user = getUserData($conn, $username);

    if (!$user) {
        header("Location: ../login.php?error=usernotfound");
        exit();
    }

    $pwdHashed = $user["usersPwd"];
    $checkPwd = password_verify($password, $pwdHashed);

    if (!$checkPwd) {
        header("Location: ../login.php?error=wrongpassword");
        exit();
    } else {
        $_SESSION["second_account"] = true;
        $_SESSION["userid"] = $user["usersId"];
        $_SESSION["usersname"] = $user["usersName"];
        $_SESSION["usersuid"] = $user["usersUid"];
        $_SESSION["usersrole"] = $user["usersrole"];
        header("Location: ../index.php");
        exit();
    }
}