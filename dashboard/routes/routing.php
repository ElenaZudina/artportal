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
    $response = HomeController::startAdminPanel();
}

elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}

//-------- Category List
elseif($path == 'categories') {
    $response = CategoryController::categoryList();
}
//-------- create category
elseif ($path == 'create-category') {
    $response = CategoryController::create();
}
elseif ($path == 'store-category') {
    $response = CategoryController::store();
}
//========= edit category
elseif ($path =='edit-category' && isset($_GET['id'])) {
    $response = CategoryController::edit($_GET['id']);
}
elseif ($path == 'result-edit-category' && isset($_GET['id'])) {
    $response = CategoryController::update($_GET['id']);
}
//========= delete category
elseif ($path =='delete-category' && isset($_GET['id'])) {
    $response = CategoryController::delete($_GET['id']);
}
elseif ($path == 'result-delete-category' && isset($_GET['id'])) {
    $response = CategoryController::destroy($_GET['id']);
}
//-------- artist moderation
elseif ($path == 'moderation-artists') {
    $response = ModerationController::pendingList();
}
elseif ($path == 'moderation-artist' && isset($_GET['id'])) {
    $response = ModerationController::viewProfile($_GET['id']);
}
elseif ($path == 'approve-artist' && isset($_GET['id'])) {
    $response = ModerationController::approve($_GET['id']);
}
elseif ($path == 'reject-artist' && isset($_GET['id'])) {
    $response = ModerationController::reject($_GET['id']);
}
//==========delete painting
elseif ($path=='paintingDel' && isset($_GET['id'])) {
    $response=controllerAdminPaintings::paintingDeleteForm($_GET['id']);
}
elseif ($path == 'paintingDelResult' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingDeleteResult($_GET['id']);
}

//-------- listPaintings
elseif($path == 'paintings') {
    $response = controllerAdminPaintings::PaintingsList();
}

//-------- add painting
elseif ($path == 'add') {
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
else 
{
    // Страница не существует
$response = HomeController::error404();
}