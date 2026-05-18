<?php
/**
 * Painting Controller - handles all painting-related page requests
 * Manages painting listings, search, pagination, categories, and painting details
 */
class PaintingController {

    /**
     * Display all paintings with pagination and search functionality
     * Retrieves search query from GET parameters, calculates pagination
     * Displays 6 paintings per page
     */
    public static function AllPaintings() {
        $perPage = 6;
        $search = trim((string)($_GET['search'] ?? ''));
        $totalItems = Paintings::getSearchPaintingsCount($search);
        
        $paginationData = PaginationHelper::getPaginationData($totalItems, $perPage);
        $pagination = $paginationData['pagination'];
        $currentPage = $pagination['currentPage'];
        $offset = $pagination['offset'];

        $arr = Paintings::getSearchPaintingsPaginated($search, $perPage, $offset);
        $searchQuery = $search;
        include_once 'views/partials/paintings.php';
        include_once 'views/allpaintings.php';
    }

    /**
     * Display paintings filtered by category
     * Retrieves category details and all paintings in that category
     * 
     * @param int $id Category ID
     */
    public static function PaintingsByCategoryID($id) {
        $arr = Paintings::getPaintingsByCategoryID($id);
        $category = Categories::getCategoryByID($id);
        include_once 'views/partials/paintings.php';
        include_once 'views/paintings_by_category.php';
    }

    /**
     * Display single painting detail page
     * Shows painting information, artist details, and favorite status
     * Handles different error cases: unpublished paintings and non-existent paintings
     * Checks if current user marked painting as favorite
     * 
     * @param int $id Painting ID
     */
    public static function PaintingByID($id) {
        $item = Paintings::getPublicPaintingByID($id);
        if (!$item) {
            $painting = Paintings::getPaintingByID($id);
            if ($painting) {
                $errorTitle = 'Painting is not available publicly';
                $errorMessage = 'This painting exists, but it is not shown in the public area because the artist has not been approved yet.';
            } else {
                $errorTitle = 'Painting not found';
                $errorMessage = 'There is no painting with this ID.';
            }

            include_once 'views/error404.php';
            return;
        }

        $isFavorite = false;
        if (isset($_SESSION['userId'])) {
            $isFavorite = Favorite::isFavorite((int)$_SESSION['userId'], (int)$id);
        }

        include_once 'views/partials/paintings.php';
        include_once 'views/viewpainting.php';
    }
}
?>
