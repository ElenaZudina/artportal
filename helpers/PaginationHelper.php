<?php
/**
 * Pagination Helper - calculates pagination parameters for paginated lists
 * Determines current page, total pages, and offset for database queries
 */
class PaginationHelper {

    /**
     * Calculate pagination data based on total items and items per page
     * Retrieves current page from GET parameter, validates range
     * Calculates offset for database queries
     * 
     * @param int $totalItems Total number of items in collection
     * @param int $perPage Number of items to display per page
     * @return array Array with pagination data (currentPage, offset, totalPages, perPage)
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

