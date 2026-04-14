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
}