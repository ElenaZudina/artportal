<?php

class CategoryController {
    //list Paintings
    public static function categoryList() {
        $arr=Categories::getCategoriesList();
        include_once 'views/category-list.php';
    }
    //--------create
    public static function create() {
        $categories = Categories::getCategoriesList();
        include_once('views/category-add-form.php');
    }

    public static function store() {
        $result = CategoryService::createCategory($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once('views/category-add-form.php');
    }
    //------------edit
    public static function edit($id) {
        $category = Categories::getCategoryByID($id);
        include_once('views/category-edit-form.php');
    }
    public static function update($id) {
        $result = CategoryService::updateCategory($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $category = Categories::getCategoryByID($id);
        include_once('views/category-edit-form.php');
    }
    //-------delete
    public static function delete($id) {
        $category = Categories::getCategoryByID($id);
        include_once('views/category-delete-form.php');
    }
    public static function destroy($id) {
        $result = CategoryService::deleteCategory($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $category = Categories::getCategoryByID($id);
        include_once('views/category-delete-form.php');
    }
}
