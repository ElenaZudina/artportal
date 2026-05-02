<?php

class CollectionController {
    public static function collectionsList() {
        $arr = Collection::getCollectionsList();
        include_once 'views/collections-list.php';
    }

    public static function create() {
        include_once 'views/collections-add-form.php';
    }

    public static function store() {
        $result = CollectionService::createCollection($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once 'views/collections-add-form.php';
    }

    public static function edit($id) {
        $collection = Collection::getCollectionById($id);
        include_once 'views/collections-edit-form.php';
    }

    public static function update($id) {
        $result = CollectionService::updateCollection($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $collection = Collection::getCollectionById($id);
        include_once 'views/collections-edit-form.php';
    }

    public static function delete($id) {
        $collection = Collection::getCollectionById($id);
        include_once 'views/collections-delete-form.php';
    }

    public static function destroy($id) {
        $result = CollectionService::deleteCollection($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $collection = Collection::getCollectionById($id);
        include_once 'views/collections-delete-form.php';
    }
}