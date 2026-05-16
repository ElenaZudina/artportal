<?php
// Файл для запуска проекта
session_start();

$timeout = 900; // 15 минут в секундах

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Время неактивности превышено — сбрасываем сессию
    unset($_SESSION['userId']);
    unset($_SESSION['status']);
    unset($_SESSION['name']);
    unset($_SESSION['last_activity']);
}
$_SESSION['last_activity'] = time();

include_once 'config/Database.php';
require 'models/Categories.php';
require 'models/Paintings.php';
require 'models/Artists.php';
require 'models/Exhibitions.php';
require 'models/Collections.php';
require 'models/Favourite.php';


require 'services/AuthService.php';
require 'services/ArtistProfileService.php';
require 'models/Register.php';

require 'models/Auth.php';

Auth::syncSessionStatus();

require 'helpers/PaginationHelper.php';

require 'models/PurchaseRequest.php';
include_once 'controllers/Controller.php';
include_once 'controllers/PaintingController.php';
include_once 'controllers/ArtistController.php';
include_once 'controllers/ExhibitionController.php';
include_once 'controllers/ProfileController.php';
include_once 'controllers/AuthController.php';
include_once 'dashboard/controllers/FavoriteController.php';
include_once 'dashboard/controllers/RequestController.php';
include_once 'routes/routing.php';

?>