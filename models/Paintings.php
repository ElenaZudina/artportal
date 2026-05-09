<?php
class Paintings{
    public static function getLastPaintings($limit = 10) {
        $limit = (int)$limit;
        $query = "
            SELECT
                paintings.*,
                artists.name AS artist_name,
                categories.name AS category
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            JOIN categories ON paintings.category_id = categories.id
            ORDER BY paintings.id DESC
            LIMIT " . $limit . "
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

    public static function getPaintingsByArtistPortfolio($id) {
        $query = "SELECT paintings.*, categories.name AS category_name
        FROM paintings
        JOIN categories ON paintings.category_id = categories.id
        WHERE paintings.artist_id = ?
        ORDER BY paintings.id DESC";
        $db = new Database();
        return $db->getAll($query, [$id]);
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

    public static function insertPainting(array $cleanData) {
        $query = "INSERT INTO paintings (title, description, image, year_created, category_id, artist_id, medium, dimensions, price, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $params = [
            $cleanData['title'],
            $cleanData['description'],
            $cleanData['image'],
            $cleanData['year_created'],
            $cleanData['category_id'],
            $cleanData['artist_id'],
            $cleanData['medium'],
            $cleanData['dimensions'],
            $cleanData['price'],
        ];

        $db = new Database();
        return $db->executeRun($query, $params);
    }

    public static function updatePainting($id, array $cleanData) {
        $query = "UPDATE paintings
        SET title = ?, description = ?, image = ?, year_created = ?, category_id = ?, medium = ?, dimensions = ?, price = ?, updated_at = NOW()
        WHERE id = ? AND artist_id = ?";

        $params = [
            $cleanData['title'],
            $cleanData['description'],
            $cleanData['image'],
            $cleanData['year_created'],
            $cleanData['category_id'],
            $cleanData['medium'],
            $cleanData['dimensions'],
            $cleanData['price'],
            $id,
            $cleanData['artist_id'],
        ];

        $db = new Database();
        return $db->executeRun($query, $params);
    }

    public static function deletePainting($id, $artistId) {
        $query = "DELETE FROM paintings WHERE id = ? AND artist_id = ?";
        $db = new Database();
        return $db->executeRun($query, [$id, $artistId]);
    }

    public static function getPaintingsByCollectionID($id) {
        // Получаем коллекцию и её параметр
        $collection = Collections::getCollectionByID($id);
        if (!$collection) {
            return [];
        }
        $db = new Database();
       
        // Динамический фильтр по ключевому слову
        $baseQuery = "SELECT paintings.*, artists.name AS artist_name, categories.name AS category_name
        FROM paintings
        JOIN artists ON paintings.artist_id = artists.id
        JOIN categories ON paintings.category_id = categories.id";

        switch ($collection['type']) {

        case 'keyword':
            $param = trim($collection['param'] ?? '');

            if ($param === '') return [];

            $query = $baseQuery . "
                WHERE paintings.title LIKE ? 
                   OR paintings.description LIKE ?
                ORDER BY paintings.id DESC";

            return $db->getAll($query, ["%$param%", "%$param%"]);

        case 'latest':
            $query = $baseQuery . "
                ORDER BY paintings.created_at DESC
                LIMIT 10";

            return $db->getAll($query);

        case 'random':
            $query = $baseQuery . "
                ORDER BY RAND()
                LIMIT 10";

            return $db->getAll($query);

        case 'popular':
            // предполагаем, что есть поле views
            $query = $baseQuery . "
                ORDER BY paintings.views DESC
                LIMIT 10";

            return $db->getAll($query);

        default:
            return [];
    }
        
    }

    
}
?>