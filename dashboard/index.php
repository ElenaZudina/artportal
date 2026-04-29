<?php
session_start();

$timeout = 900; // 15 минут в секундах

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Время неактивности превышено — сбрасываем сессию
    session_unset();
    session_destroy();
    // Редирект на страницу входа
    header("Location: artportal/login");
    exit();
}
$_SESSION['last_activity'] = time();
//session_destroy();
require_once '../config/Database.php';

include_once("modelsAdmin/modelAdmin.php");
//include_once("modelsAdmin/modelAdminPaintings.php");
//include_once("modelsAdmin/modelAdminStyle.php");

include_once("controllersAdmin/controllerAdmin.php");
//include_once("controllersAdmin/controllerAdminPaintings.php");

include('routesAdmin/routingAdmin.php'); //!!!!

//echo $response;
