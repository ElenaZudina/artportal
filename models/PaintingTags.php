<?php
/**
 * Painting Tags Model - handles relationship between paintings and tags
 * Manages painting-tag associations in database
 */
class PaintingTags {
    /**
     * Attach a tag to a painting.
     * @param int $paintingId Painting ID
     * @param int $tagId Tag ID
     * @param Database|null $db Optional database instance for testing
     * @return void
     */
    public static function attach (int $paintingId, int $tagId, $db = null): void {
        $db = $db ?? new Database();
        $query = "INSERT INTO painting_tags (painting_id, tag_id) VALUES (?, ?)";
        $db->executeRun($query, [$paintingId, $tagId]);
    }

    /**
     * Remove all tag links for a painting.
     * @param int $paintingId Painting ID
     * @param Database|null $db Optional database instance for testing
     * @return void
     */
    public static function detachByPaintingId(int $paintingId, $db = null): void {
        $db = $db ?? new Database();
        $query = "DELETE FROM painting_tags WHERE painting_id = ?";
        $db->executeRun($query, [$paintingId]);
    }
}
