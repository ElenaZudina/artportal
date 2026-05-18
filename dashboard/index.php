<?php
// Start session for dashboard
session_start();

// Set inactivity timeout (15 minutes in seconds)
$timeout = 900;

// Check for inactivity and destroy session if timeout exceeded
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Inactivity timeout exceeded — clear session and redirect to login
    session_unset();
    session_destroy();
    header("Location: /artportal/login");
    exit();
}
// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Core dependencies and helpers
require_once '../config/Database.php';
require_once '../models/Auth.php';
require_once '../helpers/ArtistHelper.php';
require_once '../helpers/CsrfHelper.php';

// Require user authentication for dashboard
Auth::requireSession();

// Include all main models
include_once("../models/Categories.php");
include_once("../models/Collections.php");
include_once("../models/Exhibitions.php");
include_once("../models/Artists.php");
include_once("../models/Paintings.php");
include_once("../models/Favourite.php");
include_once("../models/PurchaseRequest.php");
include_once("../models/Tags.php");
include_once("../models/PaintingTags.php");

// Include all main services
include_once("../services/CategoryService.php");
include_once("../services/CollectionService.php");
include_once("../services/ExhibitionService.php");
include_once("../services/ArtistProfileService.php");
include_once("../services/PaintingService.php");
include_once("../services/StatsService.php");
include_once("../services/AccountService.php");

// Include all dashboard controllers
include_once("controllers/HomeController.php");
include_once("controllers/PriceController.php");
include_once("controllers/FavoriteController.php");
include_once("controllers/RequestController.php");
include_once("controllers/ProfileController.php");
include_once("controllers/PaintingController.php");
include_once("controllers/AccountController.php");

// Route all dashboard requests
include('routes/routing.php');
