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


if ($path == 'startAdmin') {
    // Форма входа на старт Админ
    $response = AdminController::startAdminPanel();
}

elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}
/*
//-------- listPaintings
elseif($path == 'paintingsAdmin') {
    $response = controllerAdminPaintings::PaintingsList();
}
//-------- add painting
elseif ($path == 'paintingAdd') {
    $response = controllerAdminPaintings::paintingAddForm();
}
elseif ($path == 'paintingAddResult') {
    $response = controllerAdminPaintings::paintingAddResult();
}
//========= edit painting
elseif ($path =='paintingEdit' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingEditForm($_GET['id']);
}
elseif ($path == 'paintingEditResult' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingEditResult($_GET['id']);
}
//==========delete painting
elseif ($path=='paintingDel' && isset($_GET['id'])) {
    $response=controllerAdminPaintings::paintingDeleteForm($_GET['id']);
}
elseif ($path == 'paintingDelResult' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingDeleteResult($_GET['id']);
}
else 
{
    // Страница не существует
$response = controllerAdmin::error404();
}*/