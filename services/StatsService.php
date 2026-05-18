<?php
/**
 * Statistics Service - generates application statistics and analytics
 * Aggregates data for reporting and dashboard displays
 */
class StatsService {

    /**
     * Get base dashboard metrics for admin, artist, and user views.
     * @return array Counts for users, artists, pending profiles, and content entities
     */
    public static function getCounts() {
        $counts = [];
        $counts['artists'] = Auth::countByRole('artist');
        $counts['users'] = Auth::countByRole('user');
        $counts['pending_profiles'] = Artists::countPending();
        $counts['collections'] = Collections::count();
        $counts['exhibitions'] = Exhibitions::count();
        $counts['categories'] = Categories::count();
        return $counts;
    }

    /**
     * Get daily user registration totals for the selected period.
     * @param int $days Number of days to include
     * @return array Array of day and total pairs
     */
    public static function getUserGrowthByDay($days = 7) {
        $days = max(1, (int)$days);
        $db = new Database();
        $query = "SELECT DATE(created_at) AS day, COUNT(*) AS total
                  FROM users
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY day ASC";
        $rows = $db->getAll($query, [$days - 1]);

        $indexedRows = [];
        foreach ($rows as $row) {
            $indexedRows[$row['day']] = (int)($row['total'] ?? 0);
        }

        $result = [];
        $periodStart = new DateTimeImmutable('today', new DateTimeZone(date_default_timezone_get()));
        $periodStart = $periodStart->modify('-' . ($days - 1) . ' days');

        for ($offset = 0; $offset < $days; $offset++) {
            $currentDay = $periodStart->modify('+' . $offset . ' days')->format('Y-m-d');
            $result[] = [
                'day' => $currentDay,
                'total' => $indexedRows[$currentDay] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Get prepared chart data with labels and values.
     * Format: ['labels'=>array, 'values'=>array, 'periodDays'=>int]
     * @param int $days Number of days to include
     * @return array Chart labels, values, and period length
     */
    public static function getUserGrowthChartData($days = 7) {
        $rows = self::getUserGrowthByDay($days);
        $labels = [];
        $values = [];
        foreach ($rows as $r) {
            $labels[] = date('d.m', strtotime($r['day']));
            $values[] = (int)$r['total'];
        }
        return ['labels' => $labels, 'values' => $values, 'periodDays' => count($labels) ?: (int)$days];
    }

    /**
     * Get dashboard statistics for an artist account.
     * @param int $userId User ID
     * @return array Artist dashboard data
     */
    public static function getArtistDashboardStats($userId) {
        $user = Auth::getUserByID($userId);
        $artist = Artists::getArtistByUserId($userId);
        
        // Default values.
        $requests = [];
        $requestsCount = 0;
        $paintings = [];
        $paintingsCount = 0;
        $viewsTotal = 0;
        $favoritesCount = 0;
        
        if (!empty($artist['id'])) {
            $artistId = $artist['id'];
            
            // Latest 5 purchase requests.
            $requests = PurchaseRequest::getArtistRequests($artistId, 5, 0) ?? [];
            
            // Total request count.
            $allRequests = PurchaseRequest::getArtistRequests($artistId, 1000, 0) ?? [];
            $requestsCount = is_array($allRequests) ? count($allRequests) : 0;
            
            // Painting list.
            $paintings = Paintings::getPaintingsByArtistID($artistId) ?? [];
            $paintingsCount = is_array($paintings) ? count($paintings) : 0;
            
            // Total view count.
            $viewsTotal = self::calculateArtistTotalViews($artistId);
            
            // Favorites count.
            $favoritesCount = self::getArtistFavoritesCount($artistId);
        }
        
        return [
            'user' => $user,
            'artist' => $artist,
            'requests' => $requests,
            'requestsCount' => $requestsCount,
            'paintings' => $paintings,
            'paintingsCount' => $paintingsCount,
            'viewsTotal' => $viewsTotal,
            'favoritesCount' => $favoritesCount
        ];
    }

    /**
     * Get dashboard statistics for a regular user account.
     * @param int $userId User ID
     * @return array User dashboard data
     */
    public static function getUserDashboardStats($userId) {
        // Favorite paintings.
        $favorites = Favorite::getUserFavorites($userId) ?? [];
        $favoritesCount = is_array($favorites) ? count($favorites) : 0;
        
        // User purchase requests, latest 5.
        $userRequests = PurchaseRequest::getUserRequests($userId, 5) ?? [];
        
        // Total user purchase request count.
        $userRequestsCount = PurchaseRequest::getUserRequestsCount($userId);
        
        // Recent paintings from other artists.
        $recentPaintings = Paintings::getLastPaintings(6) ?? [];
        
        $user = Auth::getUserByID($userId);
        
        return [
            'user' => $user,
            'favorites' => $favorites,
            'favoritesCount' => $favoritesCount,
            'userRequests' => $userRequests,
            'userRequestsCount' => $userRequestsCount,
            'recentPaintings' => $recentPaintings
        ];
    }

    /**
     * Calculate total views for an artist's portfolio.
     * @param int $artistId Artist ID
     * @return int Total view count
     */
    private static function calculateArtistTotalViews($artistId) {
        $portfolio = Paintings::getPaintingsByArtistPortfolio($artistId) ?? [];
        $total = 0;
        
        foreach ($portfolio as $p) {
            $total += (int)($p['views'] ?? 0);
        }
        
        return $total;
    }

    /**
     * Count favorites for all paintings owned by an artist.
     * @param int $artistId Artist ID
     * @return int Favorites count
     */
    private static function getArtistFavoritesCount($artistId) {
        $db = new Database();
        $res = $db->getOne(
            'SELECT COUNT(*) AS cnt FROM favorites 
             JOIN paintings ON favorites.painting_id = paintings.id 
             WHERE paintings.artist_id = ?',
            [$artistId]
        );
        return (int)($res['cnt'] ?? 0);
    }

}

?>
