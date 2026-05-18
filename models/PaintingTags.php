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
     * @return void
     */
    public static function attach (int $paintingId, int $tagId): void {
        $db = new Database();
        $query = "INSERT INTO painting_tags (painting_id, tag_id) VALUES (?, ?)";
        $db->executeRun($query, [$paintingId, $tagId]);
    }

    /**
     * Remove all tag links for a painting.
     * @param int $paintingId Painting ID
     * @return void
     */
    public static function detachByPaintingId(int $paintingId): void {
        $db = new Database();
        $query = "DELETE FROM painting_tags WHERE painting_id = ?";
        $db->executeRun($query, [$paintingId]);
    }
}
