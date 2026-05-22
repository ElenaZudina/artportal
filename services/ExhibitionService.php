<?php

/**
 * Exhibition Service - manages art exhibition operations
 * Handles creating, updating, and managing exhibitions
 */
class ExhibitionService {
    /**
     * Create a new exhibition.
     * Validates title, linked collection, and date range before saving.
     * @param array $data Exhibition form data
     * @return array Success status with error message if failed
     */
    public static function createExhibition($data, $db = null) {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $collectionId = (int)($data['collection_id'] ?? 0);
        $startDate = trim($data['start_date'] ?? '');
        $endDate = trim($data['end_date'] ?? '');

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Exhibition title is required'];
        }

        if ($collectionId <= 0) {
            return ['success' => false, 'errorMessage' => 'Please select a collection'];
        }

        if (!Collections::getCollectionByID($collectionId, $db)) {
            return ['success' => false, 'errorMessage' => 'Selected collection does not exist'];
        }

        if ($startDate === '' || $endDate === '') {
            return ['success' => false, 'errorMessage' => 'Start date and end date are required'];
        }

        if (strtotime($startDate) === false || strtotime($endDate) === false) {
            return ['success' => false, 'errorMessage' => 'Please provide valid dates'];
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return ['success' => false, 'errorMessage' => 'Start date cannot be later than end date'];
        }

        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        if (Exhibitions::existsByTitle($title, $db)) {
            return ['success' => false, 'errorMessage' => 'Exhibition already exists'];
        }

        if (!Exhibitions::create($title, $description, $collectionId, $startDate, $endDate, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while adding exhibition'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    /**
     * Update an existing exhibition.
     * Validates title, linked collection, date range, and duplicate title.
     * @param int $id Exhibition ID
     * @param array $data Exhibition form data
     * @return array Success status with error message if failed
     */
    public static function updateExhibition($id, $data, $db = null) {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $collectionId = (int)($data['collection_id'] ?? 0);
        $startDate = trim($data['start_date'] ?? '');
        $endDate = trim($data['end_date'] ?? '');

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Exhibition title is required'];
        }

        if ($collectionId <= 0) {
            return ['success' => false, 'errorMessage' => 'Please select a collection'];
        }

        if (!Collections::getCollectionByID($collectionId, $db)) {
            return ['success' => false, 'errorMessage' => 'Selected collection does not exist'];
        }

        if ($startDate === '' || $endDate === '') {
            return ['success' => false, 'errorMessage' => 'Start date and end date are required'];
        }

        if (strtotime($startDate) === false || strtotime($endDate) === false) {
            return ['success' => false, 'errorMessage' => 'Please provide valid dates'];
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return ['success' => false, 'errorMessage' => 'Start date cannot be later than end date'];
        }

        if (Exhibitions::existsByTitleExceptId($title, $id, $db)) {
            return ['success' => false, 'errorMessage' => 'Exhibition already exists'];
        }

        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        if (!Exhibitions::updateExhibition($id, $title, $description, $collectionId, $startDate, $endDate, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while updating exhibition'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    /**
     * Delete an exhibition after confirmation.
     * @param int $id Exhibition ID
     * @param array $data Confirmation form data
     * @return array Success status with error message if failed
     */
    public static function deleteExhibition($id, $data, $db = null) {
        if (!isset($data['save'])) {
            return ['success' => false, 'errorMessage' => 'Delete action was not confirmed'];
        }

        if (!Exhibitions::deleteExhibition($id, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while deleting exhibition'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}
