<?php
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
        $query = "SELECT * FROM exhibitions WHERE start_date <= CURDATE() AND end_date >= CURDATE() ORDER BY start_date DESC LIMIT 1";
        $db = new Database();
        $arr = $db->getOne($query);
        return $arr;
    }
}