<?php

class ExhibitionController {
    public static function exhibitionsList() {
        $arr = Exhibitions::getExhibitionsList();
        include_once 'views/exhibitions-list.php';
    }

    public static function create() {
        $collections = Collections::getCollectionsList();
        include_once 'views/exhibitions-add-form.php';
    }

    public static function store() {
        $collections = Collections::getCollectionsList();
        $result = ExhibitionService::createExhibition($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once 'views/exhibitions-add-form.php';
    }

    public static function edit($id) {
        $exhibition = Exhibitions::getExhibitionById($id);
        $collections = Collections::getCollectionsList();
        include_once 'views/exhibitions-edit-form.php';
    }

    public static function update($id) {
        $collections = Collections::getCollectionsList();
        $result = ExhibitionService::updateExhibition($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $exhibition = Exhibitions::getExhibitionById($id);
        include_once 'views/exhibitions-edit-form.php';
    }

    public static function delete($id) {
        $exhibition = Exhibitions::getExhibitionById($id);
        include_once 'views/exhibitions-delete-form.php';
    }

    public static function destroy($id) {
        $result = ExhibitionService::deleteExhibition($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $exhibition = Exhibitions::getExhibitionById($id);
        include_once 'views/exhibitions-delete-form.php';
    }
}