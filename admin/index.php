<?php
// Admin entry point
session_start();

$timeout = 900; // 15 minutes in seconds

// Inactivity timeout: destroy session and redirect to login if exceeded
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: /artportal/login");
    exit();
}
$_SESSION['last_activity'] = time();

// Database and authentication
require_once '../config/Database.php';
require_once '../models/Auth.php';

Auth::requireSession('admin');

// Models
include_once("../models/Categories.php");
include_once("../models/Collections.php");
include_once("../models/Exhibitions.php");
include_once("../models/Artists.php");
include_once("../models/Paintings.php");

// Services
include_once("../services/CategoryService.php");
include_once("../services/CollectionService.php");
include_once("../services/ExhibitionService.php");
include_once("../services/StatsService.php");

// Controllers
include_once("controllers/Admin/HomeController.php");
include_once("controllers/Admin/CategoryController.php");
include_once("controllers/Admin/CollectionController.php");
include_once("controllers/Admin/ExhibitionController.php");
include_once("controllers/Admin/ModerationController.php");
include_once("controllers/Admin/UsersController.php");

// Main admin router
include('routes/routing.php');
