<?php

class Login extends Dbh {

    protected function getUser($uid, $pwd) {
        $stmt = $this->connect()->prepare('SELECT u.pwd, u.username, u.role, u.img, u.fname, u.mname, u.lname, u.staff_id, u.staff_email, u.birth_date, u.contact_num, f.fclt_id, f.fclt_name, f.region_code, f.region, f.province, f.municipality, f.fclt_type, f.fclt_contact, f.img_url, f.fclt_ref_id
        FROM facilities AS f
        INNER JOIN staff AS u ON f.fclt_id = u.fclt_id WHERE u.username = ?');

        if (!$stmt->execute([$uid])) {
            $stmt = null;
            header("Location: ../facility-login.php?error=stmtfailed");
            exit();
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            header("Location: ../facility-login.php?error=usernotfound");
            exit();
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;

        $pwdHashed = $user["pwd"];
        $checkPwd = password_verify($pwd, $pwdHashed);

        if ($checkPwd == false) {
            header("Location: ../facility-login.php?error=wrongpassword");
            exit();
        } else {
            session_start();
            $_SESSION["facilityaccount"] = true;
            $_SESSION["fcltid"] = $user["fclt_id"];
            $_SESSION["fcltname"] = $user["fclt_name"];
            $_SESSION["fclttype"] = $user["fclt_type"];
            $_SESSION["fcltcontact"] = $user["fclt_contact"];
            $_SESSION["fcltregioncode"] = $user["region_code"];
            $_SESSION["fcltregion"] = $user["region"];
            $_SESSION["fcltprovince"] = $user["province"];
            $_SESSION["fcltmunicipality"] = $user["municipality"];
            $_SESSION["fcltimg"] = $user["img_url"];
            $_SESSION["fcltuid"] = $user["fclt_ref_id"];
            $_SESSION["second_account"] = true;
            $_SESSION["usersid"] = $user["staff_id"];
            $_SESSION["usersname"] = $user["lname"] . ', ' . $user["fname"] . ' ' . $user["mname"];
            $_SESSION["usersfname"] = $user["fname"];
            $_SESSION["usersmname"] = $user["mname"];
            $_SESSION["userslname"] = $user["lname"];
            $_SESSION["usersuid"] = $user["username"];
            $_SESSION["usersrole"] = $user["role"];
            $_SESSION["usersimg"] = $user["img"];
            $_SESSION["email"] = $user["staff_email"];
            $_SESSION["usersconact"] = $user["contact_num"];
            $_SESSION["usersbday"] = $user["birth_date"];
            $status = 'Active';

            $stmt = $this->connect()->prepare('UPDATE staff SET status = :status WHERE staff_id = :staffid');
            $stmt->execute([':status' => $status, ':staffid' =>  $_SESSION["usersid"]]);

            // Check for success or failure
            if ($stmt->rowCount() > 0) {
                echo "Update successful!";
            } else {
                echo "Update failed or no rows were affected.";
            }

            $stmt = null;
        }
    }
}
