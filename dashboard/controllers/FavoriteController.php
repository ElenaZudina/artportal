<?php
require_once __DIR__ . '/../../models/Favourite.php';

class FavoriteController {

    public static function addFavorite() {
        if (empty($_SESSION['userId'])) {
            $_SESSION['errorString'] = 'You must be logged in to add paintings to favorites.';
            header('Location: /artportal/login');
            exit;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
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
        if (empty($_SESSION['userId'])) {
            $_SESSION['errorString'] = 'You must be logged in to remove paintings from favorites.';
            header('Location: /artportal/login');
            exit;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
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
        if (empty($_SESSION['userId'])) {
            $_SESSION['errorString'] = 'You must be logged in to manage favorites.';
            header('Location: /artportal/login');
            exit;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
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
