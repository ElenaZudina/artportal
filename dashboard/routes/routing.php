<?php
/**
 * Dashboard Router - routes dashboard requests to controllers
 * Parses URL path and directs to appropriate dashboard controller/action
 * Follows the style of public and admin routing for clarity and maintainability
 */

require_once __DIR__ . '/../controllers/FavoriteController.php';
require_once __DIR__ . '/../controllers/RequestController.php';

// Parse route from URL address bar
$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num = substr_count($host, '/');
$path = explode('/', $host)[$num];


// Dashboard home page (entry point)
if ($path == '' || $path == 'index.php' || $path == 'dashboard' || $path == 'startDashboard') {
    // Show dashboard home page with statistics
    $response = HomeController::startDashboard();
}
// Show artist profile page
elseif ($path == 'profile') {
    $response = ProfileController::profile();
}
// Show account info page
elseif ($path == 'account') {
    $response = AccountController::account();
}
// Show account edit form
elseif ($path == 'edit-account') {
    $response = AccountController::editAccount();
}
// Handle account update form submission
elseif ($path == 'update-account') {
    $response = AccountController::updateAccount();
}
// Show password change form
elseif ($path == 'change-password') {
    $response = AccountController::changePassword();
}
// Handle password update form submission
elseif ($path == 'update-password') {
    $response = AccountController::updatePassword();
}
// Show profile edit form
elseif ($path == 'edit-profile') {
    $response = ProfileController::editProfile();
}
// Handle profile update form submission
elseif ($path == 'update-profile') {
    $response = ProfileController::updateProfile();
}
// Show list of artist's paintings
elseif ($path == 'my-paintings') {
    $response = PaintingController::myPaintings();
}
// Show add painting form
elseif ($path == 'add-painting') {
    $response = PaintingController::addPainting();
}
// Handle add painting form submission
elseif ($path == 'store-painting') {
    $response = PaintingController::storePainting();
}
// Show edit painting form (by id)
elseif ($path == 'edit-painting' && isset($_GET['id'])) {
    $response = PaintingController::editPainting();
}
// Handle update painting form submission (by id)
elseif ($path == 'update-painting' && isset($_GET['id'])) {
    $response = PaintingController::updatePainting();
}
// Show delete painting confirmation (by id)
elseif ($path == 'delete-painting' && isset($_GET['id'])) {
    $response = PaintingController::deletePainting();
}
// Handle painting deletion (by id)
elseif ($path == 'destroy-painting' && isset($_GET['id'])) {
    $response = PaintingController::destroyPainting();
}
// Handle price calculation AJAX request
elseif ($path == 'price-calculate') {
    $response = PriceController::calculate();
}
// Show user's favorite paintings
elseif ($path == 'my-favorites') {
    $response = FavoriteController::myFavorites();
}
// Show user's purchase requests
elseif ($path == 'my-requests') {
    $response = call_user_func(['RequestController', 'myRequests']);
}
// Show purchase requests received by artist
elseif ($path == 'purchase-requests') {
    $response = call_user_func(['RequestController', 'purchaseRequests']);
}
// Logout from dashboard
elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}
// Show 404 error page for unknown routes
else {
    $response = HomeController::error404();
}
