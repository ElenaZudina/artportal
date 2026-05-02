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

    //---------add

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

    public static function getCategoryById($id) {
        $db = new Database();
        return $db->getOne("SELECT id, name FROM categories WHERE id = ?", [$id]);
    }

    public static function existsByNameExceptId($name, $id) {
        $db = new Database();
        $category = $db->getOne(
            "SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1",
            [$name, $id]
        );

        return $category ? true : false;
    }

    public static function updateCategory($id, $name) {
        $db = new Database();
        $sql = "UPDATE `categories` SET `name` = ? WHERE `id` = ?";
        $item = $db->executeRun($sql, [$name, $id]);

        return $item == true;
    }
}