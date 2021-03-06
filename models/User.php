<?php

class User
{
    public static function checkName($name)
    {
        return(strlen($name) > 1) ? true : false;
    }

    public static function checkPassword($password)
    {
        return(strlen($password) > 5) ? true : false;
    }

    public static function checkPhone($phone)
    {
        if (preg_match("/^\(?\+?[0-9]*\)?([0-9 -]+)$/", $phone)) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkEmail($email)
    {
        return(filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
    }

    public static function checkEmailExists($email)
    {
        $db = DB::getConnection();
        if ($db) {
            $sql  = "SELECT id ";
            $sql .= "FROM user ";
            $sql .= "WHERE email = :email ";
            $sql .= "LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $id = $stmt->fetchColumn();
            return($id) ? true : false;
        }
    }

    public static function register($name, $email, $password)
    {
        $passwordHash = FunctionLibrary::passwordEncrypt($password);

        $db = DB::getConnection();
        if ($db) {
            $sql  = "INSERT INTO user(";
            $sql .= "name, email, password";
            $sql .= ") VALUES(";
            $sql .= "?, ?, ?";
            $sql .= ")";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $email, PDO::PARAM_STR);
            $stmt->bindParam(3, $passwordHash, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $user = self::getUserByEmail($email);
                self::auth($user);
                return true;
            } else {
                return false;
            }
        }
    }

    public static function getUserByEmail($email){
        $db = DB::getConnection();
        if ($db) {
            $sql  = "SELECT id, name, email, password, role ";
            $sql .= "FROM user ";
            $sql .= "WHERE email = :email ";
            $sql .= "LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($user) ? $user : false;
        }
    }

    public static function getUserById($id){
        $id = intval($id);
        if ($id) {
            $db = DB::getConnection();
            if ($db) {
                $sql = "SELECT id, name, email, password ";
                $sql .= "FROM user ";
                $sql .= "WHERE id = :id ";
                $sql .= "LIMIT 1";

                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return ($user) ? $user : false;
            }
        }
    }

    public static function login($email, $password, $remember){
        $user = self::getUserByEmail($email);
        if ($user) {
            if (FunctionLibrary::passwordCheck($password, $user['password'])) {
                if ($remember == 'true') {
                    $key = '1291tramvai1q1avtobus';
                    $encrypted = FunctionLibrary::encrypted($user['email'], $key);
                    setcookie('user', $encrypted, 0x7FFFFFFF, '/');
                }
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function auth($user){
        $_SESSION['user'] = $user;
    }

    public static function isLogged(){
        if (isset($_SESSION['user'])) {
            return $_SESSION['user']['email'];
        } elseif (isset($_COOKIE['user'])) {
            $key = '1291tramvai1q1avtobus';
            $decrypted = FunctionLibrary::decrypted($_COOKIE['user'], $key);
            return $decrypted;
        } else {
            FunctionLibrary::redirectTo('/');
        }
    }

    public static function isUser(){
        if (isset($_SESSION['user']) || isset($_COOKIE['user'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function destroySessionUser()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
    }

    public static function destroyCookieUser()
    {
        if (isset($_COOKIE['user'])) {
            setcookie('user', '', time() - 3600, '/');
        }
    }

    public static function edit($id, $name, $password)
    {
        $passwordHash = FunctionLibrary::passwordEncrypt($password);

        $db = DB::getConnection();
        if ($db) {
            $sql  = "UPDATE user SET ";
            $sql .= "name = :name, ";
            $sql .= "password = :password ";
            $sql .= "WHERE id = :id ";
            $sql .= "LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return($stmt->execute());
        }
    }

    public static function getAllAdmins()
    {
        $db = DB::getConnection();
        if ($db) {
            $sql = "SELECT id, name ";
            $sql .= "FROM user ";
            $sql .= "WHERE role = 'admin' ";
            $sql .= "ORDER BY id ASC";

            if (!$result = $db->query($sql)) {
                return false;
            }

            $admins = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $admins[] = $row;
            }
            return $admins;

        }
    }

    public static function deleteAdmin($id)
    {
        $id = intval($id);
        if ($id) {
            $db = DB::getConnection();
            if ($db) {
                $sql  = "DELETE FROM user ";
                $sql .= "WHERE id = :id ";
                $sql .= "LIMIT 1";

                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                return $stmt->execute();
            }
        }
    }

    public static function registerUser($name, $email, $password)
    {
        $passwordHash = FunctionLibrary::passwordEncrypt($password);

        $db = DB::getConnection();
        if ($db) {
            $sql  = "INSERT  INTO user(";
            $sql .= "name, email, password, role";
            $sql .= ") VALUES(";
            $sql .= "?, ?, ?, 'admin'";
            $sql .= ")";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $email, PDO::PARAM_STR);
            $stmt->bindParam(3, $passwordHash, PDO::PARAM_STR);

            return $stmt->execute();
        }
    }
}