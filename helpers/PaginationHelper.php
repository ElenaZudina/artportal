<?php
class PaginationHelper {

    /**
     * Получить данные пагинации
     * 
     * @param int $totalItems - общее количество элементов
     * @param int $perPage - элементов на странице
     * @return array массив с параметрами пагинации
     */
    public static function getPaginationData($totalItems, $perPage) {
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $totalPages = (int)ceil($totalItems / $perPage);
        
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $perPage;
        
        return [
            'pagination' => [
                'currentPage' => $currentPage,
                        'offset' => $offset,
                'totalPages' => $totalPages,
                'perPage' => $perPage,
            ]
        ];
    }
}

