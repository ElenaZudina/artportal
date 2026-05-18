<?php
/**
 * Dashboard Painting Controller - manages artist's paintings
 * Handles painting CRUD operations for artists
 */
class PaintingController {

    public static function myPaintings() {
        Auth::requireSession();
        $artist = requireArtistProfile();

        $paintings = Paintings::getPaintingsByArtistPortfolio((int)$artist['id']);
        include_once('views/my-paintings.php');
    }

    public static function addPainting() {
        Auth::requireSession();
        $artist = requireArtistProfile();

        $categories = Categories::getCategoriesList();
        $formData = [];
        include_once('views/painting-form.php');
    }

    public static function storePainting() {
        Auth::requireSession();
        requireArtistProfile();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: add-painting');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: add-painting');
            exit;
        }

        $resultPainting = PaintingService::createPainting($_POST, $_FILES, (int)$_SESSION['userId']);
        $test = $resultPainting['success'] ?? false;
        $errorMessage = !empty($resultPainting['errors']) && is_array($resultPainting['errors']) ? implode(' ', $resultPainting['errors']) : null;
        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $categories = Categories::getCategoriesList();
        $formData = $resultPainting['data'] ?? $_POST;
        include_once('views/painting-form.php');
    }

    public static function editPainting() {
        Auth::requireSession();
        $artist = requireArtistProfile();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        if (!$painting || (int)($painting['artist_id'] ?? 0) !== (int)$artist['id']) {
            header('Location: my-paintings');
            exit;
        }

        $categories = Categories::getCategoriesList();
        $formData = $painting;
        include_once('views/painting-form.php');
    }

    public static function updatePainting() {
        Auth::requireSession();
        requireArtistProfile();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: my-paintings');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: edit-painting?id=' . $id);
            exit;
        }

        $resultPainting = PaintingService::updatePainting($id, $_POST, $_FILES, (int)$_SESSION['userId']);
        $test = $resultPainting['success'] ?? false;
        $errorMessage = !empty($resultPainting['errors']) && is_array($resultPainting['errors']) ? implode(' ', $resultPainting['errors']) : null;
        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        $categories = Categories::getCategoriesList();
        $formData = $resultPainting['data'] ?? $painting ?? $_POST;
        include_once('views/painting-form.php');
    }

    public static function deletePainting() {
        Auth::requireSession();
        $artist = requireArtistProfile();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        if (!$painting || (int)($painting['artist_id'] ?? 0) !== (int)$artist['id']) {
            header('Location: my-paintings');
            exit;
        }

        include_once('views/painting-delete-form.php');
    }

    public static function destroyPainting() {
        Auth::requireSession();
        requireArtistProfile();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: my-paintings');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: delete-painting?id=' . $id);
            exit;
        }

        $resultPainting = PaintingService::deletePainting($id, (int)$_SESSION['userId']);
        $test = $resultPainting['success'] ?? false;
        $errorMessage = !empty($resultPainting['errors']) && is_array($resultPainting['errors']) ? implode(' ', $resultPainting['errors']) : null;
        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        include_once('views/painting-delete-form.php');
    }

}
?>
