<?php
session_start();
//session_destroy();
require_once '../config/Database.php';

include_once("modelsAdmin/modelAdmin.php");
//include_once("modelsAdmin/modelAdminPaintings.php");
//include_once("modelsAdmin/modelAdminStyle.php");

include_once("controllersAdmin/controllerAdmin.php");
//include_once("controllersAdmin/controllerAdminPaintings.php");

include('routesAdmin/routingAdmin.php'); //!!!!

//echo $response;
