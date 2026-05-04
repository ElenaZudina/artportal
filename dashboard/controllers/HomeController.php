<?php
class HomeController {

    // Вход в Дашборд
    public static function startDashboard() {
        include_once('views/start-dashboard.php');
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