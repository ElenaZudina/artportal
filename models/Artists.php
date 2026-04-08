<?php
class Artists {
    public static function getLast10Artists() {
        $query = "SELECT * FROM artists ORDER BY id DESC LIMIT 10";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getAllArtists() {
        $query = "SELECT * FROM artists ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

     public static function getArtistByID($id) {
        $query = "SELECT * FROM artists
        WHERE artists.id = ? ";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }
}
?>