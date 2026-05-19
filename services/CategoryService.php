<?php

/**
 * Category Service - manages painting category operations
 * Handles creating, updating, and deleting categories with validation
 */
class CategoryService {
    
    /**
     * Create new painting category
     * Validates category name is not empty and not duplicate
     * @param array $data Form data with category name
     * @return array Success status with error message if failed
     */
    public static function createCategory($data, $db = null) {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return ['success' => false, 'errorMessage' => 'Category name is required'];
        }

        if (Categories::existsByName($name, $db)) {
            return ['success' => false, 'errorMessage' => 'Category already exists'];
        }

        if (!Categories::create($name, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while adding category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    /**
     * Update an existing painting category.
     * Validates category name is not empty and not duplicate.
     * @param int $id Category ID
     * @param array $data Form data with category name
     * @return array Success status with error message if failed
     */
    public static function updateCategory($id, $data, $db = null) {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return ['success' => false, 'errorMessage' => 'Category name is required'];
        }

        if (Categories::existsByNameExceptId($name, $id, $db)) {
            return ['success' => false, 'errorMessage' => 'Category already exists'];
        }

        if (!Categories::updateCategory($id, $name, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while updating category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    /**
     * Delete a painting category after confirmation.
     * @param int $id Category ID
     * @param array $data Confirmation form data
     * @return array Success status with error message if failed
     */
    public static function deleteCategory($id, $data, $db = null) {
        if (!isset($data['save'])) {
            return ['success' => false, 'errorMessage' => 'Delete action was not confirmed'];
        }

        if (!Categories::deleteCategory($id, $db)) {
            return ['success' => false, 'errorMessage' => 'Database error while deleting category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}
