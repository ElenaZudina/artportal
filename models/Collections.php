<?php
class Collections {

    public static function getCollectionByID($id) {
        $query = "SELECT * FROM collections WHERE id = ?";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }
}