<?php
// Minimal bootstrap for tests: load project autoload or required files.
require_once __DIR__ . '/../vendor/autoload.php';
// If composer autoload missing, fall back to manual requires used by index.php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/PriceCalculatorService.php';
?>