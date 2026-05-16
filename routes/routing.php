<?php
// вычислить маршрут из адресной строки
$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num = substr_count($host, '/');
$path = explode('/', $host)[$num];

$activeRoute = ($path == '' || $path == 'index' || $path == 'index.php') ? 'home' : $path;
if (!defined('ACTIVE_ROUTE')) {
    define('ACTIVE_ROUTE', $activeRoute);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($path == '' || $path == 'index' || $path == 'index.php') {
    $response = Controller::StartSite();
}
elseif($path == 'all') {
    $response = PaintingController::AllPaintings();
}
elseif($path == 'category') {
    if ($id > 0) {
        $response = PaintingController::PaintingsByCategoryID($id);
    } else {
    $response = Controller::error404();
    }
}
elseif($path == 'paintings') {
    if ($id > 0) {
        $response = PaintingController::PaintingByID($id); 
    } else {
        $response = Controller::error404();
    }
}
elseif($path == 'artists') {
    $response = ArtistController::AllArtists();
}
elseif($path == 'artist') {
    if ($id > 0) {
        $response = ArtistController::ArtistByID($id); 
    } else {
        $response = Controller::error404();
    }
}
elseif($path == 'exhibitions') {
    $response = ExhibitionController::AllExhibitions();
}
elseif($path == 'current-exhibition') {
    if ($id > 0) {
        $response = ExhibitionController::ExhibitionByID($id); 
    } else {
        $response = Controller::error404();
    }
}

//register user
elseif ($path == 'registerForm' ) {
    //form register
    $response = AuthController::registerForm();
}
elseif ($path == 'registerAnswer') {
    //register user
    $response = AuthController::registerUser();
}
elseif ($path == 'artistProfileForm') {
    $response = ProfileController::artistProfileForm();
}
elseif ($path == 'artistProfileSave') {
    $response = ProfileController::artistProfileSave();
}
elseif ($path == 'login' ) {
    // Cтраница входа
    $response = AuthController::formLoginSite();
}
elseif ($path == 'forgot-password') {
    $response = AuthController::forgotPasswordForm();
}
// ------- ВХОД в зависимости  от роли-----------------------

elseif ($path == 'auth') {
    // Форма входа
    $response = AuthController::loginAction();
}
elseif ($path == 'forgot-password-request') {
    $response = AuthController::forgotPasswordRequest();
}
elseif ($path == 'logout') {
    // Выход
    $response = AuthController::logoutAction();
}

elseif ($path == 'add-to-favorite') {
    $response = FavoriteController::addFavorite();
}

elseif ($path == 'remove-from-favorite') {
    $response = FavoriteController::removeFavorite();
}

elseif ($path == 'toggle-favorite') {
    $response = FavoriteController::toggleFavorite();
}

elseif ($path == 'purchase-request') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        call_user_func(['RequestController', 'create']);
    } else {
        $response = Controller::error404();
    }
}

//error page
else{
    $response = Controller::error404();
}

?>