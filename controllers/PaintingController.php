<?php
class PaintingController {

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

    public static function PaintingsByCategoryID($id) {
        $arr = Paintings::getPaintingsByCategoryID($id);
        $category = Categories::getCategoryByID($id);
        include_once 'views/partials/paintings.php';
        include_once 'views/paintings_by_category.php';
    }

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
        include_once 'views/partials/paintings.php';
        include_once 'views/viewpainting.php';
    }
}
?>
