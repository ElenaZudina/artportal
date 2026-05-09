<?php
$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num=substr_count($host, '/');
$path = explode('/',$host)[$num];

/*if ($path == '' OR $path == 'index.php' OR $path == 'login' ) {
    // Cтраница входа
    $response = controllerAuth::formLoginSite();
}
// ------- ВХОД в зависимости  от роли-----------------------

/*elseif ($path == 'auth') {
    // Форма входа
    $response = controllerAdmin::loginAction();
}*/


if ($path == 'startDashboard') {
    // Форма входа в Дашборд
    $response = HomeController::startDashboard();
}

elseif ($path == 'profile') {
    $response = HomeController::profile();
}

elseif ($path == 'account') {
    $response = HomeController::account();
}

elseif ($path == 'edit-account') {
    $response = HomeController::editAccount();
}

elseif ($path == 'update-account') {
    $response = HomeController::updateAccount();
}

elseif ($path == 'change-password') {
    $response = HomeController::changePassword();
}

elseif ($path == 'update-password') {
    $response = HomeController::updatePassword();
}

elseif ($path == 'edit-profile') {
    $response = HomeController::editProfile();
}

elseif ($path == 'update-profile') {
    $response = HomeController::updateProfile();
}
elseif ($path == 'my-paintings') {
    $response = HomeController::myPaintings();
}
elseif ($path == 'add-painting') {
    $response = HomeController::addPainting();
}
elseif ($path == 'store-painting') {
    $response = HomeController::storePainting();
}
elseif ($path == 'edit-painting' && isset($_GET['id'])) {
    $response = HomeController::editPainting();
}
elseif ($path == 'update-painting' && isset($_GET['id'])) {
    $response = HomeController::updatePainting();
}
elseif ($path == 'delete-painting' && isset($_GET['id'])) {
    $response = HomeController::deletePainting();
}
elseif ($path == 'destroy-painting' && isset($_GET['id'])) {
    $response = HomeController::destroyPainting();
}

elseif ($path == 'price-calculate') {
    $response = PriceController::calculate();
}

elseif ($path == 'my-favorites') {
    $response = HomeController::myFavorites();
}

elseif ($path == 'my-requests') {
    $response = HomeController::myRequests();
}

elseif ($path == 'purchase-requests') {
    $response = HomeController::purchaseRequests();
}

elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}
