<?php
class controllerAuth {

    public static function formLoginSite() {
        include_once('views/formLogin.php');
    }
    // Авторизация с учетом ролей
    public static function loginAction() {
        $login=Auth::userAuthentication();
        if ($login==true) {
            // $_SESSION["status"] = admin/artist/user
            if ($_SESSION["status"] == "admin") {
            header("Location: admin/startAdmin");
            exit;
        }
        if ($_SESSION["status"] == "artist") {
            header("Location: startArtist");
            exit;
        }
        if ($_SESSION["status"] == "user") {
            header("Location: startUser");
            exit;
        }
        }
        else{
            $_SESSION['errorString']='Incorrect username and password';
            header("Location: login");
            exit;
        }
    }

    /*// Вход в админ панель
    public static function startAdminPanel() {
        include_once('viewsAdmin/startAdmin.php');
}*/


    // Выход из аккаунта
    public static function logoutAction() {
        Auth::userLogout();
        header("Location: login");
        exit;
    }
   /* // Страница Error
    public static function error404() {
        include_once('viewsAdmin/error404.php');
    }*/
}//end class
?>