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
    public static function createCategory($data) {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return ['success' => false, 'errorMessage' => 'Category name is required'];
        }

        if (Categories::existsByName($name)) {
            return ['success' => false, 'errorMessage' => 'Category already exists'];
        }

        if (!Categories::create($name)) {
            return ['success' => false, 'errorMessage' => 'Database error while adding category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    public static function updateCategory($id, $data) {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return ['success' => false, 'errorMessage' => 'Category name is required'];
        }

        if (Categories::existsByNameExceptId($name, $id)) {
            return ['success' => false, 'errorMessage' => 'Category already exists'];
        }

        if (!Categories::updateCategory($id, $name)) {
            return ['success' => false, 'errorMessage' => 'Database error while updating category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    public static function deleteCategory($id, $data) {
        if (!isset($data['save'])) {
            return ['success' => false, 'errorMessage' => 'Delete action was not confirmed'];
        }

        if (!Categories::deleteCategory($id)) {
            return ['success' => false, 'errorMessage' => 'Database error while deleting category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}
