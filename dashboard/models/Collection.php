<?php
class Collection {
    public static function getCollectionsList() {
        $sql = "SELECT * FROM collections ORDER BY id DESC";
        $db = new Database();
        return $db->getAll($sql);
    }

    public static function existsByTitle($title) {
        $db = new Database();
        $collection = $db->getOne(
            "SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1",
            [$title]
        );

        return $collection ? true : false;
    }

    public static function create($title, $type, $param) {
        $db = new Database();
        $sql = "INSERT INTO `collections` (`title`, `type`, `param`) VALUES (?, ?, ?)";
        $item = $db->executeRun($sql, [$title, $type, $param]);

        return $item == true;
    }

    public static function getCollectionById($id) {
        $db = new Database();
        return $db->getOne("SELECT id, title, type, param FROM collections WHERE id = ?", [$id]);
    }

    public static function existsByTitleExceptId($title, $id) {
        $db = new Database();
        $collection = $db->getOne(
            "SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1",
            [$title, $id]
        );

        return $collection ? true : false;
    }

    public static function updateCollection($id, $title, $type, $param) {
        $db = new Database();
        $sql = "UPDATE `collections` SET `title` = ?, `type` = ?, `param` = ? WHERE `id` = ?";
        $item = $db->executeRun($sql, [$title, $type, $param, $id]);

        return $item == true;
    }

    public static function deleteCollection($id) {
        $db = new Database();
        $sql = "DELETE FROM `collections` WHERE `id` = ?";
        $item = $db->executeRun($sql, [$id]);

        return $item == true;
    }
}
?>