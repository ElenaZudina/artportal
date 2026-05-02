<?php

class Exhibitions {
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
}