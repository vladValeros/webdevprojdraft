<?php
class Account {
    public static function is_logged_in() {
        return isset($_SESSION['user']);
    }

    public static function has_role($role) {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === $role;
    }

    public static function redirect_if_not_logged_in($role = null) {
        if (!self::is_logged_in() || ($role && !self::has_role($role))) {
            header("Location: ../account/login.php");
            exit();
        }
    }
}
?>
