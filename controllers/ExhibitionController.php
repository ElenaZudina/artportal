<?php
/**
 * Exhibition Controller - manages exhibition-related requests
 * Displays all exhibitions and individual exhibition details
 */
class ExhibitionController {

    /**
     * Display all exhibitions page
     * Retrieves all exhibitions from database
     */
    public static function AllExhibitions() {
        $arr = Exhibitions::getAllExhibitions();
        include_once 'views/partials/exhibitions.php';
        include_once 'views/allexhibitions.php';
    }

    /**
     * Display single exhibition detail page
     * Retrieves exhibition data, associated collection, and paintings in collection
     * 
     * @param int $id Exhibition ID
     */
    public static function ExhibitionByID($id) {
        $exhibition = Exhibitions::getExhibitionByID($id);
        $collection = Collections::getCollectionByID($exhibition['collection_id']);
        $paintings = Paintings::getPaintingsByCollectionID($collection['id']);
        include_once 'views/partials/exhibitions.php';
        include_once 'views/viewexhibition.php';
    }
}
?>
