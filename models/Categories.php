<?php
class Categories {

    public static function getAllCategories() {
        $query = "SELECT * FROM categories" ;
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }
}