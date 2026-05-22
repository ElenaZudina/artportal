<?php
/**
 * Tags Model - handles database operations for painting tags
 * Manages tag data for categorizing and searching paintings
 */
class Tags {
    /**
     * Get an existing tag ID or create the tag when it does not exist.
     * @param string $name Tag name
     * @param Database|null $db Optional database instance for testing
     * @return int Tag ID
     */
    public static function getOrCreateTag(string $name, $db = null): int {
        $db = $db ?? new Database();
        $tag = $db->getOne("SELECT id FROM tags WHERE name = ?", [$name]);
        if ($tag) {
            return (int)$tag['id'];
        }
        $db->executeRun("INSERT INTO tags (name) VALUES (?)", [$name]);
        return (int)$db->getLastInsertId();
    }
}
