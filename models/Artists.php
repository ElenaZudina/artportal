<?php
class Artists {
    public static function getLast10Artists() {
        $query = "SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT 10";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getAllArtists() {
        $query = "SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC";
        $db = new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    public static function getAllArtistsCount() {
        $query = "SELECT COUNT(*) AS total FROM artists WHERE status = 'approved'";
        $db = new Database();
        $row = $db->getOne($query);
        return (int)($row['total'] ?? 0);
    }

    public static function getAllArtistsPaginated($limit, $offset) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query = "SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT " . $limit . " OFFSET " . $offset;
        $db = new Database();
        return $db->getAll($query);
    }

     public static function getPublicArtistByID($id) {
        $query = "SELECT * FROM artists
        WHERE artists.id = ? AND status = 'approved'";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    public static function getArtistByID($id) {
        $query = "SELECT * FROM artists
        WHERE artists.id = ? ";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    public static function getPendingArtists() {
        $query = "SELECT * FROM artists WHERE status = 'pending' ORDER BY created_at DESC, id DESC";
        $db = new Database();
        return $db->getAll($query);
    }

    public static function approveArtist($id) {
        $db = new Database();

        $artist = self::getArtistByID($id);
        if (!$artist) {
            return false;
        }

        $queryArtist = "UPDATE artists SET status = 'approved', updated_at = NOW() WHERE id = ?";
        $db->executeRun($queryArtist, [$id]);

        $queryUser = "UPDATE users SET role = 'artist' WHERE id = ?";
        $db->executeRun($queryUser, [$artist['user_id']]);

        return true;
    }

    public static function rejectArtist($id) {
        $query = "UPDATE artists SET status = 'rejected', updated_at = NOW() WHERE id = ?";
        $db = new Database();
        return $db->executeRun($query, [$id]);
    }

    public static function getArtistByUserId($userId) {
        $query = "SELECT * FROM artists WHERE user_id = ?";
        $db = new Database();
        return $db->getOne($query, [$userId]);
    }

    public static function insertArtistProfile($cleanData) {
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

        $db = new Database();
        return $db->executeRun($query, $params);
    }

    public static function updateArtistProfile($cleanData, $userId) {
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

        $db = new Database();
        return $db->executeRun($query, $params);
    }

    public static function countPending() {
        $db = new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM artists WHERE status = 'pending'");
        return intval($row['cnt'] ?? 0);
    }
}
?>