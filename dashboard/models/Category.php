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
}