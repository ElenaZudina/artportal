<?php
/**
 * Admin Home Controller - displays admin dashboard
 * Shows statistics and analytics for administrators
 */
class HomeController {

    /**
     * Display admin dashboard with statistics and analytics
     * Retrieves application stats and user growth metrics
     * Shows admin panel home page
     */
    public static function startAdminPanel() {
        // Collect statistics using the StatsService
        $counts = StatsService::getCounts();
        // Get daily user growth metrics for the last 7 days
        $userGrowth = StatsService::getUserGrowthByDay(7);
        // Prepare chart-ready series data via service
        $userGrowthChart = StatsService::getUserGrowthChartData(7);

        // Render the admin dashboard view
        include_once('views/startAdmin.php');
}

    /**
     * Display 404 error page
     * Renders a user-friendly error view for not found routes
     */
    public static function error404() {
        include_once('views/error404.php');
    }
}
?>