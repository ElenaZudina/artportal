<?php
/**
 * Statistics Service - generates application statistics and analytics
 * Aggregates data for reporting and dashboard displays
 */
class StatsService {

    // Возвращает набор базовых метрик для dashboard/admin/artist/user
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
     * Возвращает подготовленные данные для графика: метки и значения.
     * Формат: ['labels'=>array, 'values'=>array, 'periodDays'=>int]
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
     * Получить статистику дашборда для художника
     */
    public static function getArtistDashboardStats($userId) {
        $user = Auth::getUserByID($userId);
        $artist = Artists::getArtistByUserId($userId);
        
        // Значения по умолчанию
        $requests = [];
        $requestsCount = 0;
        $paintings = [];
        $paintingsCount = 0;
        $viewsTotal = 0;
        $favoritesCount = 0;
        
        if (!empty($artist['id'])) {
            $artistId = $artist['id'];
            
            // Последние 5 запросов на покупку
            $requests = PurchaseRequest::getArtistRequests($artistId, 5, 0) ?? [];
            
            // Общее количество запросов
            $allRequests = PurchaseRequest::getArtistRequests($artistId, 1000, 0) ?? [];
            $requestsCount = is_array($allRequests) ? count($allRequests) : 0;
            
            // Список картин
            $paintings = Paintings::getPaintingsByArtistID($artistId) ?? [];
            $paintingsCount = is_array($paintings) ? count($paintings) : 0;
            
            // Общее количество просмотров
            $viewsTotal = self::calculateArtistTotalViews($artistId);
            
            // Количество избранных
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
     * Получить статистику дашборда для обычного пользователя
     */
    public static function getUserDashboardStats($userId) {
        // Избранные картины
        $favorites = Favorite::getUserFavorites($userId) ?? [];
        $favoritesCount = is_array($favorites) ? count($favorites) : 0;
        
        // Запросы пользователя на покупку (последние 5)
        $userRequests = PurchaseRequest::getUserRequests($userId, 5) ?? [];
        
        // Общее количество запросов пользователя
        $userRequestsCount = PurchaseRequest::getUserRequestsCount($userId);
        
        // Недавние картины других художников
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
     * Вспомогательный метод: расчет общих просмотров художника
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
     * Вспомогательный метод: количество избранных у художника
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
