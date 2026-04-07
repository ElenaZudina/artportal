<?php
class Styles {

    public static function getAllStyles() {
        $query = "SELECT * FROM styles" ;
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }
}