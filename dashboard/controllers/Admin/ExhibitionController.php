<?php

class ExhibitionController {
    public static function exhibitionsList() {
        $arr = Exhibitions::getExhibitionsList();
        include_once 'views/exhibitions-list.php';
    }

    public static function create() {
        $collections = Collection::getCollectionsList();
        include_once 'views/exhibitions-add-form.php';
    }

    public static function store() {
        $collections = Collection::getCollectionsList();
        $result = ExhibitionService::createExhibition($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once 'views/exhibitions-add-form.php';
    }
}