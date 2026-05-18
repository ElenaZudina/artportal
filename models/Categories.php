<?php
/**
 * Categories Model - handles database operations for painting categories
 * Manages CRUD operations and queries for category data
 */
class Categories {

    /**
     * Get all painting categories
     * @param Database|null $db Optional database instance for testing
     * @return array Array of all categories
     */
    public static function getAllCategories($db = null) {
        $query = "SELECT * FROM categories" ;
        $db = $db ?? new Database();
        $arr = $db->getAll($query);
        return $arr;
    }

    /**
     * Get a category by ID.
     * @param int $id Category ID
     * @param Database|null $db Optional database instance for testing
     * @return array|null Category data or null if not found
     */
    public static function getCategoryByID($id, $db = null) {
        $query = "SELECT * FROM categories WHERE id = ?";
        $db = $db ?? new Database();
        $arr = $db->getOne($query, [$id]);
        return $arr;
    }

    /**
     * Get categories ordered by name for admin selectors and lists.
     * @param Database|null $db Optional database instance for testing
     * @return array Array of categories
     */
    public static function getCategoriesList($db = null) {
        $sql = "SELECT * FROM categories ORDER BY categories.name ASC";
        $db = $db ?? new Database();
        return $db->getAll($sql);
    }

    /**
     * Check whether a category name already exists.
     * @param string $name Category name
     * @param Database|null $db Optional database instance for testing
     * @return bool True when the name already exists
     */
    public static function existsByName($name, $db = null) {
        $db = $db ?? new Database();
        $category = $db->getOne(
            "SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) LIMIT 1",
            [$name]
        );
        return $category ? true : false;
    }

    /**
     * Create a new category.
     * @param string $name Category name
     * @param Database|null $db Optional database instance for testing
     * @return bool Success status
     */
    public static function create($name, $db = null) {
        $db = $db ?? new Database();
        $sql = "INSERT INTO `categories` (`name`) VALUES (?)";
        $item = $db->executeRun($sql, [$name]);
        return $item == true;
    }

    /**
     * Check whether another category already uses the same name.
     * @param string $name Category name
     * @param int $id Category ID to exclude
     * @param Database|null $db Optional database instance for testing
     * @return bool True when the name exists for a different category
     */
    public static function existsByNameExceptId($name, $id, $db = null) {
        $db = $db ?? new Database();
        $category = $db->getOne(
            "SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1",
            [$name, $id]
        );
        return $category ? true : false;
    }

    /**
     * Update a category name.
     * @param int $id Category ID
     * @param string $name New category name
     * @param Database|null $db Optional database instance for testing
     * @return bool Success status
     */
    public static function updateCategory($id, $name, $db = null) {
        $db = $db ?? new Database();
        $sql = "UPDATE `categories` SET `name` = ? WHERE `id` = ?";
        $item = $db->executeRun($sql, [$name, $id]);
        return $item == true;
    }

    /**
     * Delete a category by ID.
     * @param int $id Category ID
     * @param Database|null $db Optional database instance for testing
     * @return bool Success status
     */
    public static function deleteCategory($id, $db = null) {
        $db = $db ?? new Database();
        $sql = "DELETE FROM `categories` WHERE `id` = ?";
        $item = $db->executeRun($sql, [$id]);
        return $item == true;
    }

    /**
     * Count all categories.
     * @param Database|null $db Optional database instance for testing
     * @return int Total number of categories
     */
    public static function count($db = null) {
        $db = $db ?? new Database();
        $row = $db->getOne("SELECT COUNT(*) AS cnt FROM categories");
        return intval($row['cnt'] ?? 0);
    }
}
