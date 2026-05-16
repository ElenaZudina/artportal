<?php
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

}

?>
