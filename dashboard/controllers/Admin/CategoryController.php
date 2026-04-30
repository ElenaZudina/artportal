<?php
class CategoryController {
    //list Paintings
    public static function categoryList() {
        $arr=Category::getCategoriesList();
        include_once 'views/category-list.php';
    }/*
    //--------add
    public static function paintingAddForm() {
        $styles = modelAdminStyle::getStyleList();
        $artists = modelAdminPaintings::getArtistList();
        include_once('viewAdmin/paintingAddForm.php');
    }

    public static function paintingAddResult() {
        $test = modelAdminPaintings::getPaintingAdd();
        include_once('viewAdmin/paintingAddForm.php');
    }
    //------------edit
    public static function paintingEditForm($id) {
        $styles = modelAdminStyle::getStylelist();
        $artists = modelAdminPaintings::getArtistList();
        $detail = modelAdminPaintings::getPaintingDetail($id);
        include_once('viewAdmin/paintingEditform.php');
    }
    public static function paintingEditResult($id) {
        $test = modelAdminPaintings::getPaintingEdit($id);
        include_once('viewAdmin/paintingEditForm.php');
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
    }*/
}
