<?php
class HomeController {

    // Вход в админ панель
    public static function startAdminPanel() {
        // Собираем статистику через общий сервис
        $counts = StatsService::getCounts();
        $userGrowth = StatsService::getUserGrowthByDay(7);
        // Chart-ready data moved to service
        $userGrowthChart = StatsService::getUserGrowthChartData(7);

        // Передаем в вид
        include_once('views/startAdmin.php');
}

    // Страница Error
    public static function error404() {
        include_once('views/error404.php');
    }
}//end class
?>