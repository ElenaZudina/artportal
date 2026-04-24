<?php
class Paintings{
    public static function getLast10Paintings() {
        $query = "
            SELECT
                paintings.*,
                artists.name AS artist_name,
                categories.name AS category
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            JOIN categories ON paintings.category_id = categories.id
            ORDER BY paintings.id DESC
            LIMIT 10
        ";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getAllPaintings() {
       $query = "
            SELECT
                paintings.*,
                artists.name AS artist_name,
                categories.name AS category
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            JOIN categories ON paintings.category_id = categories.id
            ORDER BY paintings.id DESC
        ";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getPaintingsByCategoryID($id) {
        $query = "
            SELECT
                paintings.*,
                artists.name AS artist_name,
                categories.name AS category
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            JOIN categories ON paintings.category_id = categories.id
            WHERE paintings.category_id = ?
            ORDER BY paintings.id DESC
        ";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

    public static function getPaintingsByArtistID($id) {
        $query = "SELECT id, title, image FROM paintings where artist_id = ? ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

    public static function getPaintingByID($id) {
        $query = "SELECT paintings.*, categories.name AS category_name, artists.name AS artist_name, artists.picture AS artist_avatar
        FROM paintings
        JOIN categories ON paintings.category_id = categories.id
        JOIN artists ON paintings.artist_id = artists.id
        WHERE paintings.id = ? ";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    public static function getPaintingsByCollectionID($id) {
        // Получаем коллекцию и её параметр
        $collection = Collections::getCollectionByID($id);
        if (!$collection) {
            return [];
        }
        $param = $collection['param'];
        // Динамический фильтр по ключевому слову
        $query = "SELECT paintings.*, artists.name AS artist_name, categories.name AS category_name
        FROM paintings
        JOIN artists ON paintings.artist_id = artists.id
        JOIN categories ON paintings.category_id = categories.id
        WHERE paintings.title LIKE ? OR paintings.description LIKE ?
        ORDER BY paintings.id DESC";
        $db = new Database();
        $arr = $db->getAll($query, ["%$param%", "%$param%"]);
        return $arr;
    }
}
?>