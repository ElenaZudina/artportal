<?php
class PaintingTags {
    public static function attach (int $paintingId, int $tagId): void {
        $db = new Database();
        $query = "INSERT INTO painting_tags (painting_id, tag_id) VALUES (?, ?)";
        $db->executeRun($query, [$paintingId, $tagId]);
    }
}