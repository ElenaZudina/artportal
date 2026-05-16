<?php
class ArtistController {

    public static function AllArtists() {
        $perPage = 3;
        $search = trim((string)($_GET['search'] ?? ''));
        $totalItems = Artists::getSearchArtistsCount($search);
        
        $paginationData = PaginationHelper::getPaginationData($totalItems, $perPage);
        $pagination = $paginationData['pagination'];
        $currentPage = $pagination['currentPage'];
        $offset = $pagination['offset'];

        $arr = Artists::getSearchArtistsPaginated($search, $perPage, $offset);
        $searchQuery = $search;
        include_once 'views/partials/artists.php';
        include_once 'views/allartists.php';
    }

    public static function ArtistByID($id) {
        $item = Artists::getPublicArtistByID($id);
        if (!$item) {
            include_once 'views/error404.php';
            return;
        }
        $item['paintings'] = Paintings::getPaintingsByArtistID($id);
        include_once 'views/partials/artists.php';
        include_once 'views/viewartist.php';
    }
}
?>
