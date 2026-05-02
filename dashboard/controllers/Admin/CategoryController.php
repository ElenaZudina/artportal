<?php
//require_once __DIR__ . '/../../../models/Artists.php';

class CategoryController {
    //list Paintings
    public static function categoryList() {
        $arr=Category::getCategoriesList();
        include_once 'views/category-list.php';
    }
    //--------create
    public static function create() {
        $categories = Category::getCategoriesList();
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
        $category = Category::getCategoryById($id);
        include_once('views/category-edit-form.php');
    }
    public static function update($id) {
        $result = CategoryService::updateCategory($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $category = Category::getCategoryById($id);
        include_once('views/category-edit-form.php');
    }
    //-------delete
    public static function paintingDeleteForm($id) {
        $styles = modelAdminStyle::getStyleList();
        $artists = modelAdminPaintings::getArtistList();
        $detail = modelAdminPaintings::getPaintingDetail($id);
        include_once('viewAdmin/paintingDeleteForm.php');
    }
    public static function paintingDeleteResult($id) {
        $test = modelAdminPaintings::getPaintingDelete($id);
        include_once('viewAdmin/paintingDeleteForm.php');
    }
}
