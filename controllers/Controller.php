<?php
class Controller {

    public static function StartSite() {
        $arr = Paintings::getLastPaintings();
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

    public static function error404() {
        include_once 'views/error404.php';
    }
} 