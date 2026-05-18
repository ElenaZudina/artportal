<?php

/**
 * Admin Category Controller - manages painting categories
 * Handles category CRUD operations and administration
 */
class CategoryController {
    /**
     * List categories
     * Retrieves category list and renders the admin category list view
     */
    public static function categoryList() {
        $arr=Categories::getCategoriesList();
        include_once 'views/category-list.php';
    }
    /**
     * Show create category form
     * Renders the form used to add a new category
     */
    public static function create() {
        $categories = Categories::getCategoriesList();
        include_once('views/category-add-form.php');
    }

    /**
     * Handle storing a new category
     * Validates and delegates creation to CategoryService, then re-renders form
     */
    public static function store() {
        $result = CategoryService::createCategory($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once('views/category-add-form.php');
    }
    /**
     * Show edit category form
     * Loads existing category data and renders edit form
     * @param int $id Category identifier
     */
    public static function edit($id) {
        $category = Categories::getCategoryByID($id);
        include_once('views/category-edit-form.php');
    }
    /**
     * Update category
     * Applies updates via CategoryService and re-renders edit form
     * @param int $id Category identifier
     */
    public static function update($id) {
        $result = CategoryService::updateCategory($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $category = Categories::getCategoryByID($id);
        include_once('views/category-edit-form.php');
    }
    /**
     * Show delete confirmation for a category
     * Loads category data and renders delete confirmation view
     * @param int $id Category identifier
     */
    public static function delete($id) {
        $category = Categories::getCategoryByID($id);
        include_once('views/category-delete-form.php');
    }
    /**
     * Destroy category
     * Deletes the category via CategoryService and shows result
     * @param int $id Category identifier
     */
    public static function destroy($id) {
        $result = CategoryService::deleteCategory($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $category = Categories::getCategoryByID($id);
        include_once('views/category-delete-form.php');
    }
}
