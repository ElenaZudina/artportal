<?php
class Category {
    //---------List
    public static function getCategoriesList() {
        $sql = "SELECT * FROM categories ORDER BY categories.name ASC";
        $db = new Database();
        //$rows = массив данных
        $rows = $db->getAll($sql);
        //--------
        return $rows;
    }

    public static function existsByName($name) {
        $db = new Database();
        $category = $db->getOne(
            "SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) LIMIT 1",
            [$name]
        );

        return $category ? true : false;
    }

    public static function create($name) {
        $db = new Database();
        $sql = "INSERT INTO `categories` (`name`) VALUES (?)";
        $item = $db->executeRun($sql, [$name]);

        return $item == true;
    }
}