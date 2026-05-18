<?php
/**
 * Dashboard Home Controller - artist dashboard home page
 * Displays artist dashboard with their statistics and activities
 */
class HomeController {

    // Вход в Дашборд
    public static function startDashboard() {
        Auth::requireSession();

        $userId = (int)$_SESSION['userId'];
        $isArtist = isset($_SESSION['status']) && $_SESSION['status'] === 'artist';

        $data = $isArtist 
            ? StatsService::getArtistDashboardStats($userId)
            : StatsService::getUserDashboardStats($userId);

        extract($data);
        include_once('views/start-dashboard.php');
}

    // Страница Error
    public static function error404() {
        include_once('views/error404.php');
    }
}//end class
?>
