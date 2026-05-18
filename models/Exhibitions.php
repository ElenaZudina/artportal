<?php
/**
 * Exhibitions Model - handles database operations for art exhibitions
 * Manages exhibition data, status, and dates
 */
class Exhibitions {

    public static function getExhibitionByID($id) {
        $query = "SELECT * FROM exhibitions WHERE id = ?";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }
    public static function getAllExhibitions() {
        $query = "SELECT * FROM exhibitions ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }
    public static function getCurrentExhibition() {
        $query = "SELECT * FROM exhibitions WHERE start_date <= NOW() AND end_date >= NOW() ORDER BY start_date DESC LIMIT 1";
        $db = new Database();
        $arr = $db->getOne($query);
        return $arr;
    }

    public static function getExhibitionsList() {
        $sql = "SELECT e.id, e.title, e.description, e.collection_id, c.title AS collection_title, e.start_date, e.end_date
                FROM exhibitions e
                LEFT JOIN collections c ON c.id = e.collection_id
                ORDER BY e.id DESC";
        $db = new Database();
        return $db->getAll($sql);
    }

    public static function create($title, $description, $collectionId, $startDate, $endDate) {
        $db = new Database();
        $sql = "INSERT INTO `exhibitions` (`title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES (?, ?, ?, ?, ?)";
        $item = $db->executeRun($sql, [$title, $description, $collectionId, $startDate, $endDate]);
        return $item == true;
    }

    public static function existsByTitle($title) {
        $db = new Database();
        $exhibition = $db->getOne(
            "SELECT id FROM exhibitions WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1",
            [$title]
        );
        return $exhibition ? true : false;
    }

    public static function existsByTitleExceptId($title, $id) {
        $db = new Database();
        $exhibition = $db->getOne(
            "SELECT id FROM exhibitions WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1",
            [$title, $id]
        );
        return $exhibition ? true : false;
    }

    public static function updateExhibition($id, $title, $description, $collectionId, $startDate, $endDate) {
        $db = new Database();
        $sql = "UPDATE `exhibitions` SET `title` = ?, `description` = ?, `collection_id` = ?, `start_date` = ?, `end_date` = ? WHERE `id` = ?";
        $item = $db->executeRun($sql, [$title, $description, $collectionId, $startDate, $endDate, $id]);
        return $item == true;
    }

    public static function deleteExhibition($id) {
        $db = new Database();
        $sql = "DELETE FROM `exhibitions` WHERE `id` = ?";
        $item = $db->executeRun($sql, [$id]);
        return $item == true;
    }

    public static function count() {
        $db = new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM exhibitions");
        return intval($row['cnt'] ?? 0);
    }
}