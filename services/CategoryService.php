<?php

class CategoryService {
    public static function createCategory($data) {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return ['success' => false, 'errorMessage' => 'Category name is required'];
        }

        if (Category::existsByName($name)) {
            return ['success' => false, 'errorMessage' => 'Category already exists'];
        }

        if (!Category::create($name)) {
            return ['success' => false, 'errorMessage' => 'Database error while adding category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    public static function updateCategory($id, $data) {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return ['success' => false, 'errorMessage' => 'Category name is required'];
        }

        if (Category::existsByNameExceptId($name, $id)) {
            return ['success' => false, 'errorMessage' => 'Category already exists'];
        }

        if (!Category::updateCategory($id, $name)) {
            return ['success' => false, 'errorMessage' => 'Database error while updating category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }

    public static function deleteCategory($id, $data) {
        if (!isset($data['save'])) {
            return ['success' => false, 'errorMessage' => 'Delete action was not confirmed'];
        }

        if (!Category::deleteCategory($id)) {
            return ['success' => false, 'errorMessage' => 'Database error while deleting category'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}
