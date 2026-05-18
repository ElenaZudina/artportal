<?php
/**
 * Artist Controller - handles all artist-related page requests
 * Manages artist listing, searching, pagination, and artist profile pages
 */
class ArtistController {

    /**
     * Display all artists with pagination and search functionality
     * Retrieves search query from GET parameters, calculates pagination
     * and renders artists list with pagination controls
     */
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

    /**
     * Display single artist profile page with their paintings
     * Retrieves artist data by ID and related paintings
     * Shows 404 error if artist not found
     * 
     * @param int $id Artist ID
     */
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
