<?php
class ExhibitionController {

    public static function AllExhibitions() {
        $arr = Exhibitions::getAllExhibitions();
        include_once 'views/partials/exhibitions.php';
        include_once 'views/allexhibitions.php';
    }

    public static function ExhibitionByID($id) {
        $exhibition = Exhibitions::getExhibitionByID($id);
        $collection = Collections::getCollectionByID($exhibition['collection_id']);
        $paintings = Paintings::getPaintingsByCollectionID($collection['id']);
        include_once 'views/partials/exhibitions.php';
        include_once 'views/viewexhibition.php';
    }
}
?>
