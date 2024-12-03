<?php

class Login extends Dbh {

    protected function getUser($uid, $pwd) {
        $stmt = $this->connect()->prepare('SELECT usersPwd FROM users WHERE usersUid = ? OR usersEmail = ?');


        if(!$stmt->execute(array($uid, $pwd))) {
            $stmt = null;
            header("Location: ../admin-login.php?error=stmtfailed");
            exit();
        }

        if($stmt->rowCount() == 0)
        {
            $stmt = null;
            header("Location: ../admin-login.php?error=usernotfound");
            exit();
        }

        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPwd = password_verify($pwd, $pwdHashed[0]["usersPwd"]);

        if($checkPwd == false)
        {
            $stmt = null;
            header("Location: ../admin-login.php?error=wrongpassword");
            exit();
        }
        else if($checkPwd == true)
        {
            $stmt = $this->connect()->prepare('SELECT * FROM users WHERE usersUid = ? OR usersEmail = ? AND usersPwd = ?');

            if(!$stmt->execute(array($uid, $uid, $pwd))) {
                $stmt = null;
                header("Location: ../admin-login.php?error=stmtfailed");
                exit();
            }

            if($stmt->rowCount() == 0)
            {
                $stmt = null;
                header("Location: ../admin-login.php?error=usernotfound");
                exit();
            }

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION["adminaccount"] = true;
            $_SESSION["adminid"] = $user[0]["usersId"];
            $_SESSION["adminuid"] = $user[0]["usersUid"];
            $_SESSION["adminname"] = $user[0]["usersName"];
            $_SESSION["adminrole"] = $user[0]["usersrole"];
            $_SESSION["adminimg"] = $user[0]["usersImg"];
            $_SESSION["adminemail"] = $user[0]["usersEmail"];
            $_SESSION["adminfcltid"] = $user[0]["fclt_id"];

            $stmt = null;
        }

        $stmt = null;
    }
}