<?php
/**
 * Favourite Model - handles user favorite paintings operations
 * Manages user's favorited paintings list
 */
class Favorite {
    /**
     * Add a painting to a user's favorites.
     * @param int $userId User ID
     * @param int $paintingId Painting ID
     * @param Database|null $db Optional database instance for testing
     * @return bool Success status
     */
    public static function addToFavorite($userId, $paintingId, $db = null) {
        $sql = 'INSERT INTO `favorites` (user_id, painting_id) VALUES (?, ?)';
        $db = $db ?? new Database();
        return $db->executeRun($sql, [$userId, $paintingId]);
    }
    /**
     * Remove a painting from a user's favorites.
     * @param int $userId User ID
     * @param int $paintingId Painting ID
     * @param Database|null $db Optional database instance for testing
     * @return bool Success status
     */
    public static function removeFromFavorite($userId, $paintingId, $db = null) {
        $sql = 'DELETE FROM `favorites` WHERE user_id = ? AND painting_id = ?';
        $db = $db ?? new Database();
        return $db->executeRun($sql, [$userId, $paintingId]);
}
/**
 * Check whether a painting is in a user's favorites.
 * @param int $userId User ID
 * @param int $paintingId Painting ID
 * @param Database|null $db Optional database instance for testing
 * @return bool True when the painting is favorited by the user
 */
public static function isFavorite($userId, $paintingId, $db = null) {
        $query = 'SELECT id FROM `favorites` WHERE user_id = ? AND painting_id = ? LIMIT 1';
        $db = $db ?? new Database();
        return (bool)$db->getOne($query, [$userId, $paintingId]);
}
/**
 * Get all favorite paintings for a user.
 * @param int $userId User ID
 * @param Database|null $db Optional database instance for testing
 * @return array Array of favorited paintings with artist data
 */
public static function getUserFavorites($userId, $db = null) {
        $query = 'SELECT paintings.id AS painting_id,
                         paintings.title,
                         paintings.description,
                         paintings.image,
                         paintings.year_created,
                         paintings.category_id,
                         paintings.artist_id,
                         artists.name AS artist_name,
                         paintings.medium,
                         paintings.dimensions,
                         paintings.price,
                         favorites.id AS favorite_id
                  FROM paintings
                  JOIN favorites ON paintings.id = favorites.painting_id
                  JOIN artists ON paintings.artist_id = artists.id
                  WHERE favorites.user_id = ?';
        $db = $db ?? new Database();
        return $db->getAll($query, [$userId]);
}
}
