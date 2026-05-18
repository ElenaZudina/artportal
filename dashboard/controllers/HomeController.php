<?php
/**
 * Dashboard Home Controller - artist dashboard home page
 * Displays artist dashboard with their statistics and activities
 */

/**
 * Controller for the artist dashboard home page.
 * Displays dashboard statistics and error page for the user.
 */
class HomeController {

    // Dashboard entry point
    /**
     * Display the dashboard home page with statistics for the logged-in user.
     */
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

    // Error page
    /**
     * Display the 404 error page for the dashboard.
     */
    public static function error404() {
        include_once('views/error404.php');
    }
}
?>
