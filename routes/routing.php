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
    $response = Controller::AllPaintings();
}
elseif($path == 'category') {
    if ($id > 0) {
        $response = Controller::PaintingsByCategoryID($id);
    } else {
    $response = Controller::error404();
    }
}
elseif($path == 'paintings') {
    if ($id > 0) {
        $response = Controller::PaintingByID($id); 
    } else {
        $response = Controller::error404();
    }
}
elseif($path == 'artists') {
    $response = Controller::AllArtists();
}
elseif($path == 'artist') {
    if ($id > 0) {
        $response = Controller::ArtistByID($id); 
    } else {
        $response = Controller::error404();
    }
}
elseif($path == 'exhibitions') {
    $response = Controller::AllExhibitions();
}
elseif($path == 'current-exhibition') {
    if ($id > 0) {
        $response = Controller::ExhibitionByID($id); 
    } else {
        $response = Controller::error404();
    }
}

/*
elseif($path == 'insertcomment' and isset($_GET['comment'],$_GET['id'])) {
    $response = Controller::InsertComment($_GET['comment'],$_GET['id']);
}*/

//register user
elseif ($path == 'registerForm' ) {
    //form register
    $response = Controller::registerForm();
}
elseif ($path == 'registerAnswer') {
    //register user
    $response = Controller::registerUser();
}
elseif ($path == 'artistProfileForm') {
    $response = Controller::artistProfileForm();
}
elseif ($path == 'artistProfileSave') {
    $response = Controller::artistProfileSave();
}
elseif ($path == 'login' ) {
    // Cтраница входа
    $response = controllerAuth::formLoginSite();
}
elseif ($path == 'forgot-password') {
    $response = controllerAuth::forgotPasswordForm();
}
// ------- ВХОД в зависимости  от роли-----------------------

elseif ($path == 'auth') {
    // Форма входа
    $response = controllerAuth::loginAction();
}
elseif ($path == 'forgot-password-request') {
    $response = controllerAuth::forgotPasswordRequest();
}
elseif ($path == 'logout') {
    // Выход
    $response = controllerAuth::logoutAction();
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
        PurchaseRequestController::create();
    } else {
        $response = Controller::error404();
    }
}

//error page
else{
    $response = Controller::error404();
}

?>