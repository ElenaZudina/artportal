<?php

class CollectionService {
    public static function createCollection($data) {
        $title = trim($data['title'] ?? '');
        $type = trim($data['type'] ?? '');
        $param = trim($data['param'] ?? '');

        $allowedTypes = ['keyword', 'latest', 'random', 'popular'];

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Collection title is required'];
        }

        if (!in_array($type, $allowedTypes, true)) {
            return ['success' => false, 'errorMessage' => 'Please select a valid collection type'];
        }

        if (Collections::existsByTitle($title)) {
            return ['success' => false, 'errorMessage' => 'Collection already exists'];
        }

        // Collections::create() теперь возвращает ID новой коллекции (или false при ошибке)
        $newId = Collections::create($title, $type, $param);
        if (!$newId) {
            return ['success' => false, 'errorMessage' => 'Database error while adding collection'];
        }

        return ['success' => true, 'errorMessage' => null, 'id' => $newId];
    }

    public static function updateCollection($id, $data) {
        $title = trim($data['title'] ?? '');
        $type = trim($data['type'] ?? '');
        $param = trim($data['param'] ?? '');

        $allowedTypes = ['keyword', 'latest', 'random', 'popular'];

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Collection title is required'];
        }

        if (!in_array($type, $allowedTypes, true)) {
            return ['success' => false, 'errorMessage' => 'Please select a valid collection type'];
        }

        if (Collections::existsByTitleExceptId($title, $id)) {
            return ['success' => false, 'errorMessage' => 'Collection already exists'];
        }

        if (!Collections::updateCollection($id, $title, $type, $param)) {
            return ['success' => false, 'errorMessage' => 'Database error while updating collection'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    public static function deleteCollection($id, $data) {
        if (!isset($data['save'])) {
            return ['success' => false, 'errorMessage' => 'Delete action was not confirmed'];
        }

        if (!Collections::deleteCollection($id)) {
            return ['success' => false, 'errorMessage' => 'Database error while deleting collection'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}
?>