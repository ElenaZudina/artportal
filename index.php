<?php
// Файл для запуска проекта
session_start();
include_once 'config/Database.php';
require 'models/Categories.php';
require 'models/Paintings.php';
require 'models/Artists.php';
// Добавляю этот код на следующем этапе
//require 'models/Comments.php';
//require 'models/Register.php';

// Убрала view из index.php - нужно добавить в контроллер
//include_once 'views/paintings.php';
//include_once 'views/comments.php';

include_once 'controllers/Controller.php';
include_once 'routes/routing.php';

//На данной стадии реализации $response = null, так как в контроллере нет return, а только include. В дальнейшем нужно будет изменить контроллер, чтобы он возвращал строку, а не выводил ее на экран. И уже в index.php эту строку выводить на экран. Это позволит более гибко управлять выводом данных и отделить логику от представления.
//echo $response;
?>