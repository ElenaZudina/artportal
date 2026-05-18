<?php
/**
 * Admin Panel Router - controls access and routes admin requests
 * Central security: ensures user is admin before executing any admin routes
 * Parses URL path and routes to appropriate admin controllers
 */

$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num=substr_count($host, '/');
$path = explode('/',$host)[$num];

// Central security check: verify user is authenticated as admin
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!class_exists('Auth')) {
    require_once __DIR__ . '/../../models/Auth.php';
}
Auth::requireSession('admin');

// Display admin panel home page
if ($path == '' || $path == 'startAdmin' || $path == 'admin' || $path == 'index.php') {
    $response = HomeController::startAdminPanel();
}
// Display users list
elseif ($path == 'users') {
    $response = UsersController::index();
}
// Update user status
elseif ($path == 'user-status' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $response = UsersController::updateStatus();
}
// Logout from admin panel
elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}

//-------- Categories
// Display categories list
elseif($path == 'categories') {
    $response = CategoryController::categoryList();
}
//-------- Collections
// Display collections list
elseif($path == 'collections') {
    $response = CollectionController::collectionsList();
}
// Display create collection form
elseif ($path == 'create-collection') {
    $response = CollectionController::create();
}
// Store new collection
elseif ($path == 'store-collection') {
    $response = CollectionController::store();
}
// Display edit collection form
elseif ($path == 'edit-collection' && isset($_GET['id'])) {
    $response = CollectionController::edit($_GET['id']);
}
// Update collection
elseif ($path == 'result-edit-collection' && isset($_GET['id'])) {
    $response = CollectionController::update($_GET['id']);
}
// Display delete collection confirmation
elseif ($path == 'delete-collection' && isset($_GET['id'])) {
    $response = CollectionController::delete($_GET['id']);
}
// Delete collection
elseif ($path == 'result-delete-collection' && isset($_GET['id'])) {
    $response = CollectionController::destroy($_GET['id']);
}
// Store collection via AJAX
elseif ($path == 'store-collection-ajax') {
    CollectionController::storeAjax();
}
//-------- Exhibitions
// Display exhibitions list
elseif ($path == 'exhibitions') {
    $response = ExhibitionController::exhibitionsList();
}
// Display create exhibition form
elseif ($path == 'create-exhibition') {
    $response = ExhibitionController::create();
}
// Store new exhibition
elseif ($path == 'store-exhibition') {
    $response = ExhibitionController::store();
}
// Display edit exhibition form
elseif ($path == 'edit-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::edit($_GET['id']);
}
// Update exhibition
elseif ($path == 'result-edit-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::update($_GET['id']);
}
// Display delete exhibition confirmation
elseif ($path == 'delete-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::delete($_GET['id']);
}
// Delete exhibition
elseif ($path == 'result-delete-exhibition' && isset($_GET['id'])) {
    $response = ExhibitionController::destroy($_GET['id']);
}
//-------- Categories (continued)
// Display create category form
elseif ($path == 'create-category') {
    $response = CategoryController::create();
}
// Store new category
elseif ($path == 'store-category') {
    $response = CategoryController::store();
}
// Display edit category form
elseif ($path =='edit-category' && isset($_GET['id'])) {
    $response = CategoryController::edit($_GET['id']);
}
// Update category
elseif ($path == 'result-edit-category' && isset($_GET['id'])) {
    $response = CategoryController::update($_GET['id']);
}
// Display delete category confirmation
elseif ($path =='delete-category' && isset($_GET['id'])) {
    $response = CategoryController::delete($_GET['id']);
}
// Delete category
elseif ($path == 'result-delete-category' && isset($_GET['id'])) {
    $response = CategoryController::destroy($_GET['id']);
}
//-------- Artist Moderation
// Display pending artists list
elseif ($path == 'moderation-artists') {
    $response = ModerationController::pendingList();
}
// Display artist profile for moderation
elseif ($path == 'moderation-artist' && isset($_GET['id'])) {
    $response = ModerationController::viewProfile($_GET['id']);
}
// Approve artist registration
elseif ($path == 'approve-artist' && isset($_GET['id'])) {
    $response = ModerationController::approve($_GET['id']);
}
// Reject artist registration
elseif ($path == 'reject-artist' && isset($_GET['id'])) {
    $response = ModerationController::reject($_GET['id']);
}
//-------- Paintings
// Display delete painting confirmation
elseif ($path=='paintingDel' && isset($_GET['id'])) {
    $response=controllerAdminPaintings::paintingDeleteForm($_GET['id']);
}
// Delete painting
elseif ($path == 'paintingDelResult' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingDeleteResult($_GET['id']);
}
// Display add painting form
elseif ($path == 'add') {
    $response = controllerAdminPaintings::paintingAddForm();
}
// Store new painting
elseif ($path == 'paintingAddResult') {
    $response = controllerAdminPaintings::paintingAddResult();
}
// Display edit painting form
elseif ($path =='paintingEdit' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingEditForm($_GET['id']);
}
// Update painting
elseif ($path == 'paintingEditResult' && isset($_GET['id'])) {
    $response = controllerAdminPaintings::paintingEditResult($_GET['id']);
}
// Display 404 error page
else 
{
    $response = HomeController::error404();
}