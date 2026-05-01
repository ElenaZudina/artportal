<?php
class controllerAuth {

    public static function formLoginSite() {
        include_once('views/formLogin.php');
    }
    // Авторизация с учетом ролей
    public static function loginAction() {
        //$login=Auth::userAuthentication();
        $login = AuthService::login($_POST);
        if(!$login['success']){
            $_SESSION['errorString']='Incorrect username and password';
            header("Location: login");
            exit;
        }

        $user = $login['user'];

        $_SESSION['userId'] = $user['id'];
        $_SESSION['name'] = $user['username'];
        $_SESSION['status'] = $user['role'];

        
        if ($user["role"] == "admin") {
            header("Location: dashboard/startAdmin");
            exit;
        }
        if ($user["role"] == "artist") {
            header("Location: startArtist");
            exit;
        }
        if ($user["role"] == "user") {
            header("Location: startUser");
            exit;
        }
    }

    /*// Вход в админ панель
    public static function startAdminPanel() {
        include_once('viewsAdmin/startAdmin.php');
}*/


    // Выход из аккаунта
    public static function logoutAction() {
       session_unset(); // удаляет все переменные сессии
        session_destroy();
        
        header("Location: login");
        exit;
    }
   /* // Страница Error
    public static function error404() {
        include_once('viewsAdmin/error404.php');
    }*/
}//end class
?>