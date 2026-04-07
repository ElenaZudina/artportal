<?php
// вычислить маршрут из адресной строки
$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num = substr_count($host, '/');
$path = explode('/', $host)[$num];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($path == '' || $path == 'index' || $path == 'index.php') {
    $response = Controller::StartSite();
}
elseif($path == 'all') {
    $response = Controller::AllPaintings();
}
elseif($path == 'style') {
    if ($id > 0) {
        $response = Controller::PaintingsByStyleID($id);
    } else {
    $response = Controller::error404();
    }
}
elseif($path == 'paintings') {
    if ($id > 0) {
        $response = Controller::PaintingByID($id); 
    } else {
        $response = Controller::error404();
    }
}
elseif($path == 'artists') {
    $response = Controller::AllArtists();
}
elseif($path == 'artist') {
    if ($id > 0) {
        $response = Controller::ArtistByID($id); 
    } else {
        $response = Controller::error404();
    }
}
/*
elseif($path == 'insertcomment' and isset($_GET['comment'],$_GET['id'])) {
    $response = Controller::InsertComment($_GET['comment'],$_GET['id']);
}
//register user
elseif ($path == 'registerForm' ) {
    //form register
    $response = Controller::registerForm();
}
elseif ($path == 'registerAnswer') {
    //register user
    $response = Controller::registerUser();
}*/

//error page
else{
    $response = Controller::error404();
}

?>