<?php
/**
 * Tags Model - handles database operations for painting tags
 * Manages tag data for categorizing and searching paintings
 */
class Tags {
    public static function getOrCreateTag(string $name): int {
        $db = new Database();
        $tag = $db->getOne("SELECT id FROM tags WHERE name = ?", [$name]);
        if ($tag) {
            return (int)$tag['id'];
        }
        $db->executeRun("INSERT INTO tags (name) VALUES (?)", [$name]);
        return (int)$db->getLastInsertId();
    }
}