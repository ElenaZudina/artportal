<?php
/**
 * Main Application Router - directs requests to appropriate controllers
 * Parses URL path from REQUEST_URI and routes to correct handler
 * Supports passing ID parameter via GET query string
 */

// Parse route from URL address bar
// Extracts last segment of URL path to determine which route to handle
$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num = substr_count($host, '/');
$path = explode('/', $host)[$num];

$activeRoute = ($path == '' || $path == 'index' || $path == 'index.php') ? 'home' : $path;
if (!defined('ACTIVE_ROUTE')) {
    define('ACTIVE_ROUTE', $activeRoute);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Display home page
if($path == '' || $path == 'index' || $path == 'index.php') {
    $response = Controller::StartSite();
}
// Display all paintings gallery
elseif($path == 'all') {
    $response = PaintingController::AllPaintings();
}
// Display paintings filtered by category ID
elseif($path == 'category') {
    if ($id > 0) {
        $response = PaintingController::PaintingsByCategoryID($id);
    } else {
    $response = Controller::error404();
    }
}
// Display single painting by ID
elseif($path == 'paintings') {
    if ($id > 0) {
        $response = PaintingController::PaintingByID($id); 
    } else {
        $response = Controller::error404();
    }
}
// Display all artists gallery
elseif($path == 'artists') {
    $response = ArtistController::AllArtists();
}
// Display single artist profile by ID
elseif($path == 'artist') {
    if ($id > 0) {
        $response = ArtistController::ArtistByID($id); 
    } else {
        $response = Controller::error404();
    }
}
// Display all exhibitions
elseif($path == 'exhibitions') {
    $response = ExhibitionController::AllExhibitions();
}
// Display single exhibition by ID
elseif($path == 'current-exhibition') {
    if ($id > 0) {
        $response = ExhibitionController::ExhibitionByID($id); 
    } else {
        $response = Controller::error404();
    }
}

//-------- Registration and Authentication
// Display user registration form
elseif ($path == 'registerForm' ) {
    $response = AuthController::registerForm();
}
// Process user registration
elseif ($path == 'registerAnswer') {
    $response = AuthController::registerUser();
}
// Display artist profile creation form
elseif ($path == 'artistProfileForm') {
    $response = ProfileController::artistProfileForm();
}
// Save artist profile
elseif ($path == 'artistProfileSave') {
    $response = ProfileController::artistProfileSave();
}
// Display login form
elseif ($path == 'login' ) {
    $response = AuthController::formLoginSite();
}
// Display password recovery form
elseif ($path == 'forgot-password') {
    $response = AuthController::forgotPasswordForm();
}

//-------- User Actions
// Process login
elseif ($path == 'auth') {
    $response = AuthController::loginAction();
}
// Process password recovery request
elseif ($path == 'forgot-password-request') {
    $response = AuthController::forgotPasswordRequest();
}
// Process logout
elseif ($path == 'logout') {
    $response = AuthController::logoutAction();
}
//-------- Favorites Management
// Add painting to favorites
elseif ($path == 'add-to-favorite') {
    $response = FavoriteController::addFavorite();
}
// Remove painting from favorites
elseif ($path == 'remove-from-favorite') {
    $response = FavoriteController::removeFavorite();
}
// Toggle favorite status
elseif ($path == 'toggle-favorite') {
    $response = FavoriteController::toggleFavorite();
}

//-------- Purchase Requests
// Submit purchase request
elseif ($path == 'purchase-request') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        call_user_func(['RequestController', 'create']);
    } else {
        $response = Controller::error404();
    }
}

// Display 404 error page
else{
    $response = Controller::error404();
}

?>