<?php
class Categories {

    public static function getAllCategories() {
        $query = "SELECT * FROM categories" ;
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getCategoryByID($id) {
        $query = "SELECT * FROM categories WHERE id = ?";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    public static function getCategoriesList() {
        $sql = "SELECT * FROM categories ORDER BY categories.name ASC";
        $db = new Database();
        return $db->getAll($sql);
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

    public static function deleteCategory($id) {
        $db = new Database();
        $sql = "DELETE FROM `categories` WHERE `id` = ?";
        $item = $db->executeRun($sql, [$id]);
        return $item == true;
    }

    public static function count() {
        $db = new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM categories");
        return intval($row['cnt'] ?? 0);
    }
}