<?php
/**
 * Artists Model - handles all database operations for artists
 * Manages approved artists, pending approvals, searching, and CRUD operations
 */
class Artists {
    
    /**
     * Get the 10 most recently approved artists
     * @return array Array of approved artists ordered by ID descending
     */
    public static function getLast10Artists($db = null) {
        $query = "SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT 10";
        $db = $db ?? new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    /**
     * Get all approved artists
     * @return array Array of all approved artists
     */
    public static function getAllArtists($db = null) {
        $query = "SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC";
        $db = $db ?? new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    /**
     * Count total approved artists in database
     * @return int Total count of approved artists
     */
    public static function getAllArtistsCount($db = null) {
        $query = "SELECT COUNT(*) AS total FROM artists WHERE status = 'approved'";
        $db = $db ?? new Database();
        $row = $db->getOne($query);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get paginated list of approved artists
     * @param int $limit Number of records per page
     * @param int $offset Starting offset for records
     * @return array Array of approved artists for current page
     */
    public static function getAllArtistsPaginated($limit, $offset, $db = null) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query = "SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT " . $limit . " OFFSET " . $offset;
        $db = $db ?? new Database();
        return $db->getAll($query);
    }

    /**
     * Count approved artists matching search criteria
     * Searches in artist name, location, and biography
     * @param string $search Search query string
     * @return int Count of matching approved artists
     */
    public static function getSearchArtistsCount($search, $db = null) {
        $search = trim((string)$search);
        $db = $db ?? new Database();

        if ($search === '') {
            $row = $db->getOne("SELECT COUNT(*) AS total FROM artists WHERE status = 'approved'");
            return (int)($row['total'] ?? 0);
        }

        $query = "SELECT COUNT(*) AS total
                  FROM artists
                  WHERE status = 'approved'
                    AND (
                        name LIKE ?
                        OR location LIKE ?
                        OR bio LIKE ?
                    )";

        $like = '%' . $search . '%';
        $row = $db->getOne($query, [$like, $like, $like]);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get paginated list of approved artists matching search criteria
     * Searches in artist name, location, and biography
     * @param string $search Search query string
     * @param int $limit Number of records per page
     * @param int $offset Starting offset for records
     * @return array Array of matching artists for current page
     */
    public static function getSearchArtistsPaginated($search, $limit, $offset, $db = null) {
        $search = trim((string)$search);
        $limit = (int)$limit;
        $offset = (int)$offset;

        if ($search === '') {
            return self::getAllArtistsPaginated($limit, $offset, $db);
        }

        $query = "SELECT *
                  FROM artists
                  WHERE status = 'approved'
                    AND (
                        name LIKE ?
                        OR location LIKE ?
                        OR bio LIKE ?
                    )
                  ORDER BY id DESC
                  LIMIT " . $limit . " OFFSET " . $offset;

        $db = $db ?? new Database();
        $like = '%' . $search . '%';
        return $db->getAll($query, [$like, $like, $like]);
    }

    /**
     * Get single approved artist by ID
     * Only returns artists with 'approved' status
     * @param int $id Artist ID
     * @return array Artist data or null if not found/not approved
     */
    public static function getPublicArtistByID($id, $db = null) {
        $query = "SELECT * FROM artists
        WHERE artists.id = ? AND status = 'approved'";
        $db = $db ?? new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    /**
     * Get artist by ID regardless of status
     * Used for internal operations and admin functionality
     * @param int $id Artist ID
     * @return array Artist data or null if not found
     */
    public static function getArtistByID($id, $db = null) {
        $query = "SELECT * FROM artists
        WHERE artists.id = ? ";
        $db = $db ?? new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    /**
     * Get all artists pending approval
     * @return array Array of pending artists ordered by creation date
     */
    public static function getPendingArtists($db = null) {
        $query = "SELECT * FROM artists WHERE status = 'pending' ORDER BY created_at DESC, id DESC";
        $db = $db ?? new Database();
        return $db->getAll($query);
    }

    /**
     * Approve artist profile and update user role to 'artist'
     * Changes artist status from 'pending' to 'approved'
     * @param int $id Artist ID
     * @return bool Success status
     */
    public static function approveArtist($id, $db = null) {
        $db = $db ?? new Database();

        $artist = self::getArtistByID($id, $db);
        if (!$artist) {
            return false;
        }

        $queryArtist = "UPDATE artists SET status = 'approved', updated_at = NOW() WHERE id = ?";
        $db->executeRun($queryArtist, [$id]);

        $queryUser = "UPDATE users SET role = 'artist' WHERE id = ?";
        $db->executeRun($queryUser, [$artist['user_id']]);

        return true;
    }

    /**
     * Reject artist profile and revert user role to 'user'
     * Changes artist status to 'rejected'
     * @param int $id Artist ID
     * @return bool Success status
     */
    public static function rejectArtist($id, $db = null) {
        $db = $db ?? new Database();

        $artist = self::getArtistByID($id, $db);
        if (!$artist) {
            return false;
        }

        $queryArtist = "UPDATE artists SET status = 'rejected', updated_at = NOW() WHERE id = ?";
        $db->executeRun($queryArtist, [$id]);

        $queryUser = "UPDATE users SET role = 'user' WHERE id = ?";
        $db->executeRun($queryUser, [$artist['user_id']]);

        return true;
    }

    /**
     * Get artist profile by user ID
     * @param int $userId User ID
     * @return array Artist data or null if not found
     */
    public static function getArtistByUserId($userId, $db = null) {
        $query = "SELECT * FROM artists WHERE user_id = ?";
        $db = $db ?? new Database();
        return $db->getOne($query, [$userId]);
    }

    /**
     * Create an artist profile from validated form data.
     * @param array $cleanData Validated artist profile data
     * @return bool Success status
     */
    public static function insertArtistProfile($cleanData, $db = null) {
        $query = "INSERT INTO artists (name, location, birth_date, bio, picture, status, user_id, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $params = [
            $cleanData['name'],
            $cleanData['location'],
            $cleanData['birth_date'],
            $cleanData['bio'],
            $cleanData['picture'],
            $cleanData['status'],
            $cleanData['user_id']
        ];

        $db = $db ?? new Database();
        return $db->executeRun($query, $params);
    }

    /**
     * Update the artist profile attached to a user account.
     * @param array $cleanData Validated artist profile data
     * @param int $userId User ID that owns the artist profile
     * @return bool Success status
     */
    public static function updateArtistProfile($cleanData, $userId, $db = null) {
        $query = "UPDATE artists
                  SET name = ?, location = ?, birth_date = ?, bio = ?, picture = ?, status = ?, updated_at = NOW()
                  WHERE user_id = ?";

        $params = [
            $cleanData['name'],
            $cleanData['location'],
            $cleanData['birth_date'],
            $cleanData['bio'],
            $cleanData['picture'],
            $cleanData['status'],
            $userId
        ];

        $db = $db ?? new Database();
        return $db->executeRun($query, $params);
    }

    /**
     * Count artist profiles waiting for moderation.
     * @return int Number of pending artists
     */
    public static function countPending($db = null) {
        $db = $db ?? new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM artists WHERE status = 'pending'");
        return intval($row['cnt'] ?? 0);
    }
}
?>
