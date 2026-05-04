<?php
class HomeController {

    /*public static function formLoginSite() {
        include_once('viewsAdmin/formLogin.php');
    }
    // Форма авторизации с учетом ролей
    public static function loginAction() {
        $login=modelAdmin::userAuthentication();
        if ($login==true) {
            // $_SESSION["status"] = admin/artist/user
            if ($_SESSION["status"] == "admin") {
            header("Location: startAdmin");
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
    }*/

    // Вход в админ панель
    public static function startAdminPanel() {
        include_once('views/startAdmin.php');
}


  /*  // Выход из админ панели
    public static function logoutAction() {
        modelAdmin::userLogout();
        header("Location: login");
        exit;
    }*/
    // Страница Error
    public static function error404() {
        include_once('views/error404.php');
    }
}//end class
?>