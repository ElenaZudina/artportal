<?php
require_once __DIR__ . '/../controllers/FavoriteController.php';
require_once __DIR__ . '/../controllers/RequestController.php';

$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num=substr_count($host, '/');
$path = explode('/',$host)[$num];


if ($path == '' || $path == 'index.php' || $path == 'dashboard' || $path == 'startDashboard') {
    // Форма входа в Дашборд
    $response = HomeController::startDashboard();
}

elseif ($path == 'profile') {
    $response = ProfileController::profile();
}

elseif ($path == 'account') {
    $response = AccountController::account();
}

elseif ($path == 'edit-account') {
    $response = AccountController::editAccount();
}

elseif ($path == 'update-account') {
    $response = AccountController::updateAccount();
}

elseif ($path == 'change-password') {
    $response = AccountController::changePassword();
}

elseif ($path == 'update-password') {
    $response = AccountController::updatePassword();
}

elseif ($path == 'edit-profile') {
    $response = ProfileController::editProfile();
}

elseif ($path == 'update-profile') {
    $response = ProfileController::updateProfile();
}
elseif ($path == 'my-paintings') {
    $response = PaintingController::myPaintings();
}
elseif ($path == 'add-painting') {
    $response = PaintingController::addPainting();
}
elseif ($path == 'store-painting') {
    $response = PaintingController::storePainting();
}
elseif ($path == 'edit-painting' && isset($_GET['id'])) {
    $response = PaintingController::editPainting();
}
elseif ($path == 'update-painting' && isset($_GET['id'])) {
    $response = PaintingController::updatePainting();
}
elseif ($path == 'delete-painting' && isset($_GET['id'])) {
    $response = PaintingController::deletePainting();
}
elseif ($path == 'destroy-painting' && isset($_GET['id'])) {
    $response = PaintingController::destroyPainting();
}

elseif ($path == 'price-calculate') {
    $response = PriceController::calculate();
}

elseif ($path == 'my-favorites') {
    $response = FavoriteController::myFavorites();
}

elseif ($path == 'my-requests') {
    $response = call_user_func(['RequestController', 'myRequests']);
}

elseif ($path == 'purchase-requests') {
    $response = call_user_func(['RequestController', 'purchaseRequests']);
}

elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}

else {
    // Страница не существует
    $response = HomeController::error404();
}
