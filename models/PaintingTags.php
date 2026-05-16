<?php
class PaintingTags {
    public static function attach (int $paintingId, int $tagId): void {
        $db = new Database();
        $query = "INSERT INTO painting_tags (painting_id, tag_id) VALUES (?, ?)";
        $db->executeRun($query, [$paintingId, $tagId]);
    }

    public static function detachByPaintingId(int $paintingId): void {
        $db = new Database();
        $query = "DELETE FROM painting_tags WHERE painting_id = ?";
        $db->executeRun($query, [$paintingId]);
    }
}