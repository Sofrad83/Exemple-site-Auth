<?php
namespace App\Support;

class Auth {
    private static $dbQuery;
    private static $sessionDuration;
    private static $user;

    public static function init($dbQuery, $sessionDuration = 120){
        self::$dbQuery = $dbQuery;
        self::$sessionDuration = $sessionDuration;
        session_start();
    }

    public static function login($username, $password, $remember = false) {
        $user = self::getUserByUsername($username);
        if ($user && User::verifyPassword($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['last_activity'] = time();
            $_SESSION['expire_time'] = time() + (self::$sessionDuration * 60);

            if ($remember) {
                self::setRememberMe($user);
            }

            self::$user = $user;

            return true;
        }
        return false;
    }

    public static function user()
    {
        return self::$user;
    }

    private static function setRememberMe($user) {
        $token = bin2hex(random_bytes(16));
        setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), "/"); // Cookie valable 30 jours

        self::$dbQuery->execute("UPDATE user SET token = :token WHERE id = :id", ['token' => $token, 'id' => $user->id]);
    }

    public static function checkRememberMe() {
        if (isset($_COOKIE['remember_me'])) {
            $token = $_COOKIE['remember_me'];
            $stmt = self::$dbQuery->query("SELECT * FROM user WHERE token = :token", ['token' => $token]);
            $user = $stmt[0];

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['last_activity'] = time();
                $_SESSION['expire_time'] = time() + (self::$sessionDuration * 60);
                return true;
            }
        }
        return false;
    }

    public static function logout() {
        session_unset();
        session_destroy();

        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, "/"); // Expire le cookie
            self::$dbQuery->execute("UPDATE user SET token = NULL WHERE token = :token", ['token' => $_COOKIE['remember_me']]);
        }
    }

    public static function isLoggedIn() {
        if (isset($_SESSION['user_id']) && (time() < $_SESSION['expire_time'])) {
            $_SESSION['last_activity'] = time();
            $_SESSION['expire_time'] = time() + (self::$sessionDuration * 60);
            return true;
        } elseif (self::checkRememberMe()) {
            return true;
        }
        return false;
    }

    public static function getUserById($id) {
        $res = self::$dbQuery->query("SELECT * FROM user WHERE id = :id", ['id' => $id]);
        $user = $res[0] ?? null;
        if ($user) {
            return new User($user['id'], $user['username'], $user['email'], $user['password']);
        }
        return null;
    }

    public static function getUserByUsername($username) {
        $res = self::$dbQuery->query("SELECT * FROM user WHERE username = :username", ['username' => $username]);
        $user = $res[0];
        if ($user) {
            return new User($user['id'], $user['username'], $user['email'], $user['password']);
        }
        return null;
    }
}
