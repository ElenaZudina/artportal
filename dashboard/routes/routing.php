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


if ($path == '' OR $path == 'index.php' OR $path == 'login') {
    // Форма входа в Дашборд
    $response = HomeController::startDashboard();
}

elseif ($path == 'logout') {
    header('Location: /artportal/logout');
    exit;
}
