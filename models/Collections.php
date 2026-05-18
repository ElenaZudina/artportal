<?php
/**
 * Collections Model - handles database operations for painting collections
 * Manages collection data and related queries
 */
class Collections {

    /**
     * Get a collection by ID.
     * @param int $id Collection ID
     * @return array|null Collection data or null if not found
     */
    public static function getCollectionByID($id) {
        $query = "SELECT * FROM collections WHERE id = ?";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    /**
     * Get all collections ordered for admin listing.
     * @return array Array of collections
     */
    public static function getCollectionsList() {
        $sql = "SELECT * FROM collections ORDER BY id DESC";
        $db = new Database();
        return $db->getAll($sql);
    }

    /**
     * Check whether a collection title already exists.
     * @param string $title Collection title
     * @return bool True when the title already exists
     */
    public static function existsByTitle($title) {
        $db = new Database();
        $collection = $db->getOne(
            "SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1",
            [$title]
        );
        return $collection ? true : false;
    }

    /**
     * Create a new collection.
     * @param string $title Collection title
     * @param string $type Collection type
     * @param string|null $param Collection parameter
     * @return int|false New collection ID or false on failure
     */
    public static function create($title, $type, $param) {
        $db = new Database();
        $sql = "INSERT INTO `collections` (`title`, `type`, `param`) VALUES (?, ?, ?)";
        $stmt = $db->executeRun($sql, [$title, $type, $param]);
        return $stmt ? $db->getLastInsertId() : false;
    }

    /**
     * Check whether another collection already uses the same title.
     * @param string $title Collection title
     * @param int $id Collection ID to exclude
     * @return bool True when the title exists for a different collection
     */
    public static function existsByTitleExceptId($title, $id) {
        $db = new Database();
        $collection = $db->getOne(
            "SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1",
            [$title, $id]
        );
        return $collection ? true : false;
    }

    /**
     * Update collection metadata.
     * @param int $id Collection ID
     * @param string $title Collection title
     * @param string $type Collection type
     * @param string|null $param Collection parameter
     * @return bool Success status
     */
    public static function updateCollection($id, $title, $type, $param) {
        $db = new Database();
        $sql = "UPDATE `collections` SET `title` = ?, `type` = ?, `param` = ? WHERE `id` = ?";
        $item = $db->executeRun($sql, [$title, $type, $param, $id]);
        return $item == true;
    }

    /**
     * Delete a collection by ID.
     * @param int $id Collection ID
     * @return bool Success status
     */
    public static function deleteCollection($id) {
        $db = new Database();
        $sql = "DELETE FROM `collections` WHERE `id` = ?";
        $item = $db->executeRun($sql, [$id]);
        return $item == true;
    }

    /**
     * Count all collections.
     * @return int Total number of collections
     */
    public static function count() {
        $db = new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM collections");
        return intval($row['cnt'] ?? 0);
    }
}
