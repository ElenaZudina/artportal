<?php
class Controller {

    public static function StartSite() {
        $arr = Paintings::getLast10Paintings();
        $artistArr = Artists::getLast10Artists();
        $exhibition = Exhibitions::getCurrentExhibition();

        $sliderPaintings = [];

        if($exhibition) {
            $collection = Collections::getCollectionByID($exhibition['collection_id']);
            $sliderPaintings = Paintings::getPaintingsByCollectionID($collection['id']);
            }
        include_once 'views/partials/paintings.php';// Подключаем представление для отображения списка картин на главной странице
        include_once 'views/partials/artists.php';// Подключаем представление для отображения списка художников на главной странице
        include_once 'views/partials/slider.php';
        include_once 'views/home.php';// Подключаем представление для отображения главной страницы, если нужно добавить дополнительный контент
    }

    public static function AllCategories() {
        $arr = Categories::getAllCategories();
        include_once 'views/partials/menu_categories.php';
    }

    public static function AllPaintings() {
        $arr = Paintings::getAllPaintings();
        include_once 'views/partials/paintings.php';
        include_once 'views/allpaintings.php';
    }

     public static function PaintingsByCategoryID($id) {
        $arr = Paintings::getPaintingsByCategoryID($id);
        $category = Categories::getCategoryByID($id);
        include_once 'views/partials/paintings.php';
        include_once 'views/paintings_by_category.php';
    }

    public static function PaintingByID($id) {
        $item = Paintings::getPaintingByID($id);
        include_once 'views/partials/paintings.php';
        include_once 'views/viewpainting.php';
    }

     public static function AllArtists() {
        $arr = Artists::getAllArtists();
        include_once 'views/partials/artists.php';
        include_once 'views/allartists.php';
    }

    public static function ArtistByID($id) {
        $item = Artists::getArtistByID($id);
        $item['paintings'] = Paintings::getPaintingsByArtistID($id);
        include_once 'views/partials/artists.php';
        include_once 'views/viewartist.php';
    }

    public static function AllExhibitions() {
        $arr = Exhibitions::getAllExhibitions();
        include_once 'views/partials/exhibitions.php';
        include_once 'views/allexhibitions.php';
    }

      public static function ExhibitionByID($id) {
        $exhibition = Exhibitions::getExhibitionByID($id);
        $collection = Collections::getCollectionByID($exhibition['collection_id']);
        $paintings = Paintings::getPaintingsByCollectionID($collection['id']);
        include_once 'views/partials/exhibitions.php';
        include_once 'views/viewexhibition.php';
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
    }*/
    // Регистрация
    public static function registerForm() {
        include_once('views/formRegister.php');
    }
    public static function registerUser() {
        $result = RegisterService::register($_POST);

        if (!empty($result['success']) && !empty($result['user'])) {
            $_SESSION['userId'] = $result['user']['id'];
            $_SESSION['name'] = $result['user']['username'];
            $_SESSION['status'] = $result['user']['role'];
        }

        include_once('views/answerRegister.php');
    }

    public static function artistProfileForm() {
        if (empty($_SESSION['userId'])) {
            header('Location: /artportal/login');
            exit;
        }

        $existingProfile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        include_once('views/formArtistProfile.php');
    }

    public static function artistProfileSave() {
        if (empty($_SESSION['userId'])) {
            header('Location: /artportal/login');
            exit;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/artistProfileForm');
            exit;
        }

        $resultArtist = ArtistProfileService::createProfile($_POST, $_FILES, (int)$_SESSION['userId']);
        if (!empty($resultArtist['success'])) {
            include_once('views/answerArtistProfile.php');
            return;
        }

        $existingProfile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $formData = $resultArtist['data'] ?? [];
        include_once('views/formArtistProfile.php');
    }
} //end class