<?php

require_once("../dbc.php");

class UserLogic
{
    /**
     * ユーザーを登録する
     * @param array $userData
     * @return bool result
     */
    public static function createUser($userData)
    {
        $result = false;
        $sql = "INSERT INTO users(name,email,password) VALUES (?,?,?)";

        $arr = [];
        $arr[] = $userData["username"];
        $arr[] = $userData["email"];
        $arr[] = password_hash($userData["password"], PASSWORD_DEFAULT);

        try {
            $stmt = connect()->prepare($sql);
            return $result = $stmt->execute($arr);
        } catch (Exception $e) {
            echo $e->getMessage();
            return $result;
        }
    }

    /**
     * ユーザーを登録する
     * @param string $email,password
     * @return bool result
     */
    public static function login($email, $password)
    {
        $result = false;

        $user = self::getUserByEmail($email);


        if (!$user) {
            $_SESSION["msg"] = "emailが一致しません";
            return $result;
        }

        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["login_user"] = $user;
            $result = true;
            return $result;
        }

        $_SESSION["msg"] = "パスワードが一致しません";
        return $result;
    }


    /**
     * emailからユーザーを取得
     * @param string $email
     * @return array|bool $user|false
     */
    public static function getUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";

        $arr = [];
        $arr[] = $email;

        try {
            $stmt = connect()->prepare($sql);
            $stmt->execute($arr);
            return $user = $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * ログインチェック
     * @param void
     * @return bool $result
     */
    public static function checkLogin()
    {
        $result = false;

        //セッションにログインユーザーが入っていなかったらfalse
        if (isset($_SESSION["login_user"]) && $_SESSION["login_user"]["id"] > 0) {
            return $result = true;
        }


        return $result;
    }

    /**
     * ログアウト処理
     */
    public static function logout()
    {
        $_SESSION = array();
        session_destroy();
    }
}
