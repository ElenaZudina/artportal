<?php
session_start();

$timeout = 900; // 15 минут в секундах

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Время неактивности превышено — сбрасываем сессию
    session_unset();
    session_destroy();
    // Редирект на страницу входа
    header("Location: /artportal/login");
    exit();
}
$_SESSION['last_activity'] = time();

//session_destroy();
require_once '../config/Database.php';
require_once '../models/Auth.php';

Auth::requireSession('admin');

include_once("../models/Categories.php");
include_once("../models/Collections.php");
include_once("../models/Exhibitions.php");
include_once("../models/Artists.php");
include_once("../models/Paintings.php");
include_once("../services/CategoryService.php");
include_once("../services/CollectionService.php");
include_once("../services/ExhibitionService.php");
include_once("../services/StatsService.php");

include_once("controllers/Admin/HomeController.php");
include_once("controllers/Admin/CategoryController.php");
include_once("controllers/Admin/CollectionController.php");
include_once("controllers/Admin/ExhibitionController.php");
include_once("controllers/Admin/ModerationController.php");
include_once("controllers/Admin/UsersController.php");

include('routes/routing.php');
