<?php
class Paintings{
    public static function getLast10Paintings() {
        $query = "SELECT * FROM paintings ORDER BY id DESC LIMIT 10";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getAllPaintings() {
        $query = "SELECT * FROM paintings ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getPaintingsByStyleID($id) {
        $query = "SELECT * FROM paintings where style_id = ? ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

     public static function getPaintingByID($id) {
        $query = "SELECT paintings.*, styles.name AS style_name, artists.name as artist_name, users.username
        FROM paintings
        JOIN styles ON paintings.style_id = styles.id
        JOIN artists ON paintings.artist_id = artists.id
        JOIN users ON artists.user_id = users.id
        WHERE paintings.id = ? ";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }
}
?>