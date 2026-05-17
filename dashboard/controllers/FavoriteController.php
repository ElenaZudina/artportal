<?php
require_once __DIR__ . '/../../models/Favourite.php';

class FavoriteController {

    public static function myFavorites() {
        Auth::requireSession('user');

        $favorites = Favorite::getUserFavorites((int)$_SESSION['userId']);
        include_once('views/my-favorites.php');
    }

    public static function addFavorite() {
        Auth::requireSession('user', 'Only users can add paintings to favorites.');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $paintingId = (int)($_POST['painting_id'] ?? 0);

        if ($paintingId <= 0) {
            header('Location: /artportal/');
            exit;
        }

        if (!Favorite::isFavorite($userId, $paintingId)) {
            Favorite::addToFavorite($userId, $paintingId);
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
        header('Location: ' . $redirect);
        exit;
    }

    public static function removeFavorite() {
        Auth::requireSession('user', 'Only users can manage their favorites.');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $paintingId = (int)($_POST['painting_id'] ?? 0);

        if ($paintingId <= 0) {
            header('Location: /artportal/');
            exit;
        }

        Favorite::removeFromFavorite($userId, $paintingId);

        $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
        header('Location: ' . $redirect);
        exit;
    }

    public static function toggleFavorite() {
        Auth::requireSession('user', 'Only users can add paintings to favorites.');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $paintingId = (int)($_POST['painting_id'] ?? 0);

        if ($paintingId <= 0) {
            header('Location: /artportal/');
            exit;
        }

        if (Favorite::isFavorite($userId, $paintingId)) {
            Favorite::removeFromFavorite($userId, $paintingId);
        } else {
            Favorite::addToFavorite($userId, $paintingId);
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
        header('Location: ' . $redirect);
        exit;
    }
}
?>
