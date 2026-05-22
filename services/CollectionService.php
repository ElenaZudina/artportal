<?php

/**
 * Collection Service - manages painting collection operations
 * Handles CRUD operations and validation for collections
 */
class CollectionService {
    /**
     * Create a new painting collection.
     * Validates title, collection type, and duplicate titles.
     * @param array $data Collection form data
     * @return array Success status with new ID or error message
     */
    public static function createCollection($data, $db = null) {
        $title = trim($data['title'] ?? '');
        $type = trim($data['type'] ?? '');
        $param = trim($data['param'] ?? '');

        $allowedTypes = ['keyword', 'latest', 'random', 'popular', 'ai'];

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Collection title is required'];
        }

        if (!in_array($type, $allowedTypes, true)) {
            return ['success' => false, 'errorMessage' => 'Please select a valid collection type'];
        }

        if (Collections::existsByTitle($title, $db)) {
            return ['success' => false, 'errorMessage' => 'Collection already exists'];
        }

        // Collections::create() returns the new collection ID, or false on failure.
        $newId = Collections::create($title, $type, $param, $db);
        if (!$newId) {
            return ['success' => false, 'errorMessage' => 'Database error while adding collection'];
        }

        return ['success' => true, 'errorMessage' => null, 'id' => $newId];
    }

    /**
     * Update an existing painting collection.
     * Validates title, collection type, and duplicate titles.
     * @param int $id Collection ID
     * @param array $data Collection form data
     * @return array Success status with error message if failed
     */
    public static function updateCollection($id, $data, $db = null) {
        $title = trim($data['title'] ?? '');
        $type = trim($data['type'] ?? '');
        $param = trim($data['param'] ?? '');

        $allowedTypes = ['keyword', 'latest', 'random', 'popular', 'ai'];

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Collection title is required'];
        }

        if (!in_array($type, $allowedTypes, true)) {
            return ['success' => false, 'errorMessage' => 'Please select a valid collection type'];
        }

        if (Collections::existsByTitleExceptId($title, $id, $db)) {
            return ['success' => false, 'errorMessage' => 'Collection already exists'];
        }

        if (!Collections::updateCollection($id, $title, $type, $param, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while updating collection'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    /**
     * Delete a painting collection after confirmation.
     * @param int $id Collection ID
     * @param array $data Confirmation form data
     * @return array Success status with error message if failed
     */
    public static function deleteCollection($id, $data, $db = null) {
        if (!isset($data['save'])) {
            return ['success' => false, 'errorMessage' => 'Delete action was not confirmed'];
        }

        if (!Collections::deleteCollection($id, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while deleting collection'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}
?>
