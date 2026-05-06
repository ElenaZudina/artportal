<?php
class Favorite {
    public static function addToFavorite($userId, $paintingId) {
        $sql = 'INSERT INTO `favorites` (user_id, painting_id) VALUES (?, ?)';
        $db = new Database();
        return $db->executeRun($sql, [$userId, $paintingId]);
    }
    public static function removeFromFavorite($userId, $paintingId) {
        $sql = 'DELETE FROM `favorites` WHERE user_id = ? AND painting_id = ?';
        $db = new Database();
        return $db->executeRun($sql, [$userId, $paintingId]);
}
public static function isFavorite($userId, $paintingId) {
        $query = 'SELECT id FROM `favorites` WHERE user_id = ? AND painting_id = ? LIMIT 1';
        $db = new Database();
        return (bool)$db->getOne($query, [$userId, $paintingId]);
}
public static function getUserFavorites($userId) {
        $query = 'SELECT * FROM paintings JOIN favorites ON paintings.id = favorites.painting_id WHERE favorites.user_id = ?';
        $db = new Database();
        return $db->getAll($query, [$userId]);
}
}