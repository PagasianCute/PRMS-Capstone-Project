<?php

class LoginContr extends Login {

    private $uid;
    private $pwd;

    public function __construct($uid, $pwd)
    {
        $this->uid = $uid;
        $this->pwd = $pwd;
    }

    public function loginUser() {
        if($this->emptyInput() == false) {
            header("Location:../admin-login.php?error=emptyInput");
            exit();
        }
        
        $this->getUser($this->uid, $this->pwd);

        header("Location: ../../super_admin/dashboard/admin-index.php?error=none");
        exit();

    }

    private function emptyInput() {
        $result = true;
        if(empty($this->uid) || empty($this->pwd))
        {
            $result = false;
        }
        else
        {
            $result = true;
        }
        return $result;
    }
}