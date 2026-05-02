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

     public static function getArtistByID($id) {
        $query = "SELECT * FROM artists
        WHERE artists.id = ? AND status = 'approved' ";
        $db = new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    public static function getArtistByIDAny($id) {
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
        $query = "UPDATE artists SET status = 'approved', updated_at = NOW() WHERE id = ?";
        $db = new Database();
        return $db->executeRun($query, [$id]);
    }

    public static function rejectArtist($id) {
        $query = "UPDATE artists SET status = 'rejected', updated_at = NOW() WHERE id = ?";
        $db = new Database();
        return $db->executeRun($query, [$id]);
    }
}
?>