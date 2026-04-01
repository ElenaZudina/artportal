<?php
// Файл для запуска проекта
session_start();
include_once 'config/Database.php';
require 'model/Style.php';
require 'model/Paintings.php';
// Добавляю этот код на следующем этапе
//require 'model/Comments.php';
//require 'model/Register.php';

// Убрала view из index.php - нужно добавить в контроллер
//include_once 'view/paintings.php';
//include_once 'view/comments.php';

include_once 'controller/Controller.php';
include_once 'route/routing.php';

//На данной стадии реализации $response = null, так как в контроллере нет return, а только include. В дальнейшем нужно будет изменить контроллер, чтобы он возвращал строку, а не выводил ее на экран. И уже в index.php эту строку выводить на экран. Это позволит более гибко управлять выводом данных и отделить логику от представления.
//echo $response;
?>