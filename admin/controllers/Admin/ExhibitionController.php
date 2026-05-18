<?php

/**
 * Admin Exhibition Controller - manages art exhibitions
 * Handles exhibition CRUD operations and event management
 */
class ExhibitionController {
    /**
     * List exhibitions
     * Retrieves exhibitions list and renders admin view
     */
    public static function exhibitionsList() {
        $arr = Exhibitions::getExhibitionsList();
        include_once 'views/exhibitions-list.php';
    }

    /**
     * Show create exhibition form
     * Loads collections for selection and renders add form
     */
    public static function create() {
        $collections = Collections::getCollectionsList();
        include_once 'views/exhibitions-add-form.php';
    }

    /**
     * Handle exhibition creation
     * Delegates creation to ExhibitionService and re-renders form with result
     */
    public static function store() {
        $collections = Collections::getCollectionsList();
        $result = ExhibitionService::createExhibition($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once 'views/exhibitions-add-form.php';
    }

    /**
     * Show edit exhibition form
     * Loads exhibition and available collections for editing
     * @param int $id Exhibition identifier
     */
    public static function edit($id) {
        $exhibition = Exhibitions::getExhibitionById($id);
        $collections = Collections::getCollectionsList();
        include_once 'views/exhibitions-edit-form.php';
    }

    /**
     * Update exhibition
     * Applies updates via ExhibitionService and re-renders edit form
     * @param int $id Exhibition identifier
     */
    public static function update($id) {
        $collections = Collections::getCollectionsList();
        $result = ExhibitionService::updateExhibition($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $exhibition = Exhibitions::getExhibitionById($id);
        include_once 'views/exhibitions-edit-form.php';
    }

    /**
     * Show delete confirmation for exhibition
     * Loads exhibition data and renders confirmation page
     * @param int $id Exhibition identifier
     */
    public static function delete($id) {
        $exhibition = Exhibitions::getExhibitionById($id);
        include_once 'views/exhibitions-delete-form.php';
    }

    /**
     * Destroy exhibition
     * Deletes exhibition via ExhibitionService and shows result
     * @param int $id Exhibition identifier
     */
    public static function destroy($id) {
        $result = ExhibitionService::deleteExhibition($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $exhibition = Exhibitions::getExhibitionById($id);
        include_once 'views/exhibitions-delete-form.php';
    }
}