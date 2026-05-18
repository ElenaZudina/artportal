<?php
require_once __DIR__ . '/../../models/Favourite.php';

/**
 * Dashboard Favorite Controller - manages artist's favorite collections
 * Handles favorite painting management for artists
 */

/**
 * Controller for managing user's favorite paintings in the dashboard.
 * Handles adding, removing, toggling, and displaying favorite paintings.
 */
class FavoriteController {

    /**
     * Display the list of favorite paintings for the logged-in user.
     */
    public static function myFavorites() {
        Auth::requireSession('user');

        $favorites = Favorite::getUserFavorites((int)$_SESSION['userId']);
        include_once('views/my-favorites.php');
    }

    /**
     * Add a painting to the user's favorites.
     * Validates POST method and CSRF token.
     */
    public static function addFavorite() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        Auth::requireUserAction('Only users can add paintings to favorites.');

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

    /**
     * Remove a painting from the user's favorites.
     * Validates POST method and CSRF token.
     */
    public static function removeFavorite() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        Auth::requireUserAction('Only users can manage their favorites.');

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

    /**
     * Toggle the favorite status of a painting for the user.
     * Validates POST method and CSRF token.
     */
    public static function toggleFavorite() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        Auth::requireUserAction('Only users can add paintings to favorites.');

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
