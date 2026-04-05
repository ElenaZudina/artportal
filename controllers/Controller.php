<?php
class Controller {

    public static function StartSite() {
        $arr = Paintings::getLast10Paintings();
        include_once 'views/start.php';
    }

    public static function AllStyle() {
        $arr = Styles::getAllStyles();
        include_once 'views/style.php';
    }

    public static function AllPaintings() {
        $arr = Paintings::getAllPaintings();
        include_once 'views/allpaintings.php';
    }

     public static function PaintingsByStyleID($id) {
        $arr = Paintings::getPaintingsByStyleID($id);
        include_once 'views/paintings_by_style.php';
    }

    public static function PaintingByID($id) {
        $item = Paintings::getPaintingByID($id);
        include_once 'views/viewpainting.php';
    }

    public static function error404() {
        include_once 'views/error404.php';
    }
/*
    public static function InsertComment($c, $id) {
        Comments::InsertComment($c, $id);
        //self::NewsByID($id);
        header('Location:paintings?id='.$id.'#ctable');
    }
    // Список комментариев
    public static function Comments($paintingid) {
        $arr = Comments::getCommentByPaintingID($paintingid);
        ViewComments::CommentsByPainting($arr);
    }
    // количество комментариев к картине
    public static function CommentsCount($paintingid) {
        $arr = Comments::getCommentsCountByPaintingID($paintingid);
        ViewComments::CommentsCount($arr);
    }
    // Ссылка - переход к списку комментариев
    public static function CommentsCountWithAncor($paintingid) {
        $arr = Comments::getCommentsCountByPaintingID($paintingid);
        ViewComments::CommentsCountWithAncor($arr);
    }
    // Регистрация
    public static function registerForm() {
        include_once('view/formRegister.php');
    }
    public static function registerUser() {
        $result = Register::registerUser();
        include_once('view/answerRegister.php');
    }*/
} //end class