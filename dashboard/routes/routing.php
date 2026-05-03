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
//-------- Collections List
elseif($path == 'collections') {
    $response = CollectionController::collectionsList();
}
elseif ($path == 'create-collection') {
    $response = CollectionController::create();
}
elseif ($path == 'store-collection') {
    $response = CollectionController::store();
}
elseif ($path == 'edit-collection' && isset($_GET['id'])) {
    $response = CollectionController::edit($_GET['id']);
}
elseif ($path == 'result-edit-collection' && isset($_GET['id'])) {
    $response = CollectionController::update($_GET['id']);
}
elseif ($path == 'delete-collection' && isset($_GET['id'])) {
    $response = CollectionController::delete($_GET['id']);
}
elseif ($path == 'result-delete-collection' && isset($_GET['id'])) {
    $response = CollectionController::destroy($_GET['id']);
}
elseif ($path == 'store-collection-ajax') {
    // AJAX-обработка для создания коллекции из модального окна
    CollectionController::storeAjax();
}
elseif ($path == 'exhibitions') {
    $response = ExhibitionController::exhibitionsList();
}
elseif ($path == 'create-exhibition') {
    $response = ExhibitionController::create();
}
elseif ($path == 'store-exhibition') {
    $response = ExhibitionController::store();
}
elseif ($path == 'edit-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::edit($_GET['id']);
}
elseif ($path == 'result-edit-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::update($_GET['id']);
}
elseif ($path == 'delete-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::delete($_GET['id']);
}
elseif ($path == 'result-delete-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::destroy($_GET['id']);
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