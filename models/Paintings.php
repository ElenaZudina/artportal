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

    public static function getPaintingsByCategoryID($id) {
        $query = "SELECT * FROM paintings where category_id = ? ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

    public static function getPaintingsByArtistID($id) {
        $query = "SELECT title, image FROM paintings where artist_id = ? ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

    public static function getPaintingByID($id) {
        $query = "SELECT paintings.*, categories.name AS category_name, artists.name as artist_name, users.username
        FROM paintings
        JOIN categories ON paintings.category_id = categories.id
        JOIN artists ON paintings.artist_id = artists.id
        JOIN users ON artists.user_id = users.id
        WHERE paintings.id = ? ";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }
}
?>