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
}
