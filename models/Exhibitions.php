<?php
/**
 * Exhibitions Model - handles database operations for art exhibitions
 * Manages exhibition data, status, and dates
 */
class Exhibitions {

    /**
     * Get an exhibition by ID.
     * @param int $id Exhibition ID
     * @return array|null Exhibition data or null if not found
     */
    public static function getExhibitionByID($id) {
        $query = "SELECT * FROM exhibitions WHERE id = ?";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }
    /**
     * Get all exhibitions ordered by newest first.
     * @return array Array of exhibitions
     */
    public static function getAllExhibitions() {
        $query = "SELECT * FROM exhibitions ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }
    /**
     * Get the exhibition active at the current date and time.
     * @return array|null Current exhibition data or null if none is active
     */
    public static function getCurrentExhibition() {
        $query = "SELECT * FROM exhibitions WHERE start_date <= NOW() AND end_date >= NOW() ORDER BY start_date DESC LIMIT 1";
        $db = new Database();
        $arr = $db->getOne($query);
        return $arr;
    }

    /**
     * Get exhibitions with their linked collection titles.
     * @return array Array of exhibitions for admin listing
     */
    public static function getExhibitionsList() {
        $sql = "SELECT e.id, e.title, e.description, e.collection_id, c.title AS collection_title, e.start_date, e.end_date
                FROM exhibitions e
                LEFT JOIN collections c ON c.id = e.collection_id
                ORDER BY e.id DESC";
        $db = new Database();
        return $db->getAll($sql);
    }

    /**
     * Create a new exhibition.
     * @param string $title Exhibition title
     * @param string $description Exhibition description
     * @param int $collectionId Linked collection ID
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return bool Success status
     */
    public static function create($title, $description, $collectionId, $startDate, $endDate) {
        $db = new Database();
        $sql = "INSERT INTO `exhibitions` (`title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES (?, ?, ?, ?, ?)";
        $item = $db->executeRun($sql, [$title, $description, $collectionId, $startDate, $endDate]);
        return $item == true;
    }

    /**
     * Check whether an exhibition title already exists.
     * @param string $title Exhibition title
     * @return bool True when the title already exists
     */
    public static function existsByTitle($title) {
        $db = new Database();
        $exhibition = $db->getOne(
            "SELECT id FROM exhibitions WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1",
            [$title]
        );
        return $exhibition ? true : false;
    }

    /**
     * Check whether another exhibition already uses the same title.
     * @param string $title Exhibition title
     * @param int $id Exhibition ID to exclude
     * @return bool True when the title exists for a different exhibition
     */
    public static function existsByTitleExceptId($title, $id) {
        $db = new Database();
        $exhibition = $db->getOne(
            "SELECT id FROM exhibitions WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1",
            [$title, $id]
        );
        return $exhibition ? true : false;
    }

    /**
     * Update exhibition data.
     * @param int $id Exhibition ID
     * @param string $title Exhibition title
     * @param string $description Exhibition description
     * @param int $collectionId Linked collection ID
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return bool Success status
     */
    public static function updateExhibition($id, $title, $description, $collectionId, $startDate, $endDate) {
        $db = new Database();
        $sql = "UPDATE `exhibitions` SET `title` = ?, `description` = ?, `collection_id` = ?, `start_date` = ?, `end_date` = ? WHERE `id` = ?";
        $item = $db->executeRun($sql, [$title, $description, $collectionId, $startDate, $endDate, $id]);
        return $item == true;
    }

    /**
     * Delete an exhibition by ID.
     * @param int $id Exhibition ID
     * @return bool Success status
     */
    public static function deleteExhibition($id) {
        $db = new Database();
        $sql = "DELETE FROM `exhibitions` WHERE `id` = ?";
        $item = $db->executeRun($sql, [$id]);
        return $item == true;
    }

    /**
     * Count all exhibitions.
     * @return int Total number of exhibitions
     */
    public static function count() {
        $db = new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM exhibitions");
        return intval($row['cnt'] ?? 0);
    }
}
