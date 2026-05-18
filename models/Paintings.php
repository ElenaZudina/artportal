<?php
/**
 * Paintings Model - handles database operations for paintings
 * Manages painting data, search, pagination, categories, and CRUD operations
 * Only displays paintings from approved artists
 */
class Paintings{
    
    /**
     * Get the most recently added paintings from approved artists
     * @param int $limit Number of paintings to retrieve (default: 10)
     * @return array Array of paintings with artist and category information
     */
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
            WHERE artists.status = 'approved'
            ORDER BY paintings.id DESC
            LIMIT " . $limit . "
        ";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    /**
     * Get all paintings from approved artists
     * @return array Array of all paintings with artist and category information
     */
    public static function getAllPaintings() {
       $query = "
            SELECT
                paintings.*,
                artists.name AS artist_name,
                categories.name AS category
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            JOIN categories ON paintings.category_id = categories.id
            WHERE artists.status = 'approved'
            ORDER BY paintings.id DESC
        ";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    /**
     * Count total paintings from approved artists
     * @return int Total count of paintings
     */
    public static function getAllPaintingsCount() {
        $query = "
            SELECT COUNT(*) AS total
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            WHERE artists.status = 'approved'
        ";
        $db = new Database();
        $row = $db->getOne($query);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get paginated list of paintings from approved artists
     * @param int $limit Number of paintings per page
     * @param int $offset Starting offset for pagination
     * @return array Array of paintings for current page with artist and category data
     */
    public static function getAllPaintingsPaginated($limit, $offset) {
       $limit = (int)$limit;
       $offset = (int)$offset;
       $query = "
            SELECT
                paintings.*, 
                artists.name AS artist_name,
                categories.name AS category
            FROM paintings
            JOIN artists ON paintings.artist_id = artists.id
            JOIN categories ON paintings.category_id = categories.id
            WHERE artists.status = 'approved'
            ORDER BY paintings.id DESC
            LIMIT " . $limit . " OFFSET " . $offset . "
       ";
       $db = new Database();
       return $db->getAll($query);
    }

    /**
     * Count paintings matching search criteria from approved artists
     * Searches in painting name and description
     * @param string $search Search query string
     * @return int Count of matching paintings
     */
    public static function getSearchPaintingsCount($search) {
        $search = trim((string)$search);
        $db = new Database();

        if ($search === '') {
            $row = $db->getOne("SELECT COUNT(*) AS total FROM paintings JOIN artists ON paintings.artist_id = artists.id WHERE artists.status = 'approved'");
            return (int)($row['total'] ?? 0);
        }

        $query = "SELECT COUNT(*) AS total
                  FROM paintings
                  JOIN artists ON paintings.artist_id = artists.id
                  WHERE artists.status = 'approved'
                    AND (
                        paintings.title LIKE ?
                        OR paintings.description LIKE ?
                                                OR artists.name LIKE ?
                    )";

        $like = '%' . $search . '%';
                $row = $db->getOne($query, [$like, $like, $like]);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get paginated paintings matching search criteria from approved artists.
     * Searches in painting title, description, and artist name.
     * @param string $search Search query string
     * @param int $limit Number of paintings per page
     * @param int $offset Starting offset for pagination
     * @return array Array of matching paintings with artist and category data
     */
    public static function getSearchPaintingsPaginated($search, $limit, $offset) {
        $search = trim((string)$search);
        $limit = (int)$limit;
        $offset = (int)$offset;

        if ($search === '') {
            return self::getAllPaintingsPaginated($limit, $offset);
        }

        $query = "SELECT paintings.*,
                         artists.name AS artist_name,
                         categories.name AS category
                  FROM paintings
                  JOIN artists ON paintings.artist_id = artists.id
                  JOIN categories ON paintings.category_id = categories.id
                  WHERE artists.status = 'approved'
                    AND (
                        paintings.title LIKE ?
                        OR paintings.description LIKE ?
                                                OR artists.name LIKE ?
                    )
                  ORDER BY paintings.id DESC
                  LIMIT " . $limit . " OFFSET " . $offset;

        $db = new Database();
        $like = '%' . $search . '%';
                return $db->getAll($query, [$like, $like, $like]);
    }

    /**
     * Get public paintings by category ID.
     * Only includes paintings from approved artists.
     * @param int $id Category ID
     * @return array Array of paintings in the category
     */
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
              AND artists.status = 'approved'
            ORDER BY paintings.id DESC
        ";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

    /**
     * Get compact painting records for an artist.
     * @param int $id Artist ID
     * @return array Array of painting IDs, titles, and images
     */
    public static function getPaintingsByArtistID($id) {
        $query = "SELECT id, title, image FROM paintings where artist_id = ? ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query, [$id]);
        return $arr;
    }

    /**
     * Get portfolio paintings for an artist with category names.
     * @param int $id Artist ID
     * @return array Array of portfolio paintings
     */
    public static function getPaintingsByArtistPortfolio($id) {
        $query = "SELECT paintings.*, categories.name AS category_name
        FROM paintings
        JOIN categories ON paintings.category_id = categories.id
        WHERE paintings.artist_id = ?
        ORDER BY paintings.id DESC";
        $db = new Database();
        return $db->getAll($query, [$id]);
    }

    /**
     * Get a painting by ID regardless of artist approval status.
     * @param int $id Painting ID
     * @return array|null Painting data or null if not found
     */
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

    /**
     * Get a public painting by ID from an approved artist.
     * @param int $id Painting ID
     * @return array|null Painting data or null if not found or not public
     */
    public static function getPublicPaintingByID($id) {
        $query = "SELECT paintings.*, categories.name AS category_name, artists.name AS artist_name, artists.picture AS artist_avatar
                FROM paintings
                JOIN categories ON paintings.category_id = categories.id
                JOIN artists ON paintings.artist_id = artists.id
                WHERE paintings.id = ?
                  AND artists.status = 'approved'";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    /**
     * Find a painting by uploaded file hash.
     * @param string|null $fileHash File hash
     * @return array|null Painting data or null if not found
     */
    public static function getPaintingByFileHash($fileHash) {
        if (empty($fileHash)) {
            return null;
        }
        $query = "SELECT id, title, image FROM paintings WHERE file_hash = ? LIMIT 1";
        $db = new Database();
        return $db->getOne($query, [$fileHash]);
    }

    /**
     * Create a painting from validated form data.
     * @param array $cleanData Validated painting data
     * @return int New painting ID
     */
    public static function insertPainting(array $cleanData) {
        $fileHash = $cleanData['file_hash'] ?? null;
        
        if ($fileHash) {
            $query = "INSERT INTO paintings (title, description, image, file_hash, year_created, category_id, artist_id, medium, dimensions, price, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $params = [
                $cleanData['title'],
                $cleanData['description'],
                $cleanData['image'],
                $fileHash,
                $cleanData['year_created'],
                $cleanData['category_id'],
                $cleanData['artist_id'],
                $cleanData['medium'],
                $cleanData['dimensions'],
                $cleanData['price'],
            ];
        } else {
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
        }

        $db = new Database();
        $db->executeRun($query, $params);
        return $db->getLastInsertId();
    }

    /**
     * Update an artist-owned painting.
     * @param int $id Painting ID
     * @param array $cleanData Validated painting data
     * @return bool Success status
     */
    public static function updatePainting($id, array $cleanData) {
        $fileHash = $cleanData['file_hash'] ?? null;
        
        if ($fileHash) {
            $query = "UPDATE paintings
            SET title = ?, description = ?, image = ?, file_hash = ?, year_created = ?, category_id = ?, medium = ?, dimensions = ?, price = ?, updated_at = NOW()
            WHERE id = ? AND artist_id = ?";

            $params = [
                $cleanData['title'],
                $cleanData['description'],
                $cleanData['image'],
                $fileHash,
                $cleanData['year_created'],
                $cleanData['category_id'],
                $cleanData['medium'],
                $cleanData['dimensions'],
                $cleanData['price'],
                $id,
                $cleanData['artist_id'],
            ];
        } else {
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
        }

        $db = new Database();
        return $db->executeRun($query, $params);
    }

    /**
     * Delete a painting owned by an artist.
     * @param int $id Painting ID
     * @param int $artistId Artist ID
     * @return bool Success status
     */
    public static function deletePainting($id, $artistId) {
        $query = "DELETE FROM paintings WHERE id = ? AND artist_id = ?";
        $db = new Database();
        return $db->executeRun($query, [$id, $artistId]);
    }

    /**
     * Get paintings generated by a collection rule.
     * Supports keyword, latest, random, popular, and AI tag-based collections.
     * @param int $id Collection ID
     * @return array Array of paintings for the collection
     */
    public static function getPaintingsByCollectionID($id) {
        // Load the collection and its parameter before building the query.
        $collection = Collections::getCollectionByID($id);
        if (!$collection) {
            return [];
        }
        $db = new Database();
       
        // Build a dynamic filter based on the collection type.
        $baseQuery = "SELECT paintings.*, artists.name AS artist_name, categories.name AS category_name
        FROM paintings
        JOIN artists ON paintings.artist_id = artists.id
        JOIN categories ON paintings.category_id = categories.id";

        switch ($collection['type']) {

        case 'keyword':
            $param = trim($collection['param'] ?? '');

                if ($param === '') return [];

                $query = $baseQuery . "
                     WHERE artists.status = 'approved'
                     AND (paintings.title LIKE ? 
                         OR paintings.description LIKE ?)
                     ORDER BY paintings.id DESC";

            return $db->getAll($query, ["%$param%", "%$param%"]);

        case 'latest':
            $query = $baseQuery . "
                WHERE artists.status = 'approved'
                ORDER BY paintings.created_at DESC
                LIMIT 10";

            return $db->getAll($query);

        case 'random':
            $query = $baseQuery . "
                WHERE artists.status = 'approved'
                ORDER BY RAND()
                LIMIT 10";

            return $db->getAll($query);

        case 'popular':
            // Assumes the paintings table has a views column.
            $query = $baseQuery . "
                WHERE artists.status = 'approved'
                ORDER BY paintings.views DESC
                LIMIT 10";

            return $db->getAll($query);

        case 'ai':

            $param = trim($collection['param'] ?? '');

            if ($param === '') return [];

            $keywords = array_values(array_filter(explode(' ', strtolower($param))));

            if (empty($keywords)) return [];

            $placeholders = implode(',', array_fill(0, count($keywords), '?'));

            $query = $baseQuery . "
            JOIN painting_tags pt ON paintings.id = pt.painting_id
            JOIN tags t ON pt.tag_id = t.id
            WHERE artists.status = 'approved'
            AND t.name IN ($placeholders)
            GROUP BY paintings.id
            ORDER BY COUNT(t.id) DESC
            LIMIT 10";

            return $db->getAll($query, $keywords);

        default:
            return [];
    }
        
    }

    
}
?>
