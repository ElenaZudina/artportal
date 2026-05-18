<?php
// Minimal bootstrap for tests: load project autoload or required files.
require_once __DIR__ . '/../vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionPath = __DIR__ . '/../build/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0777, true);
    }

    session_save_path($sessionPath);
    session_start();
}

// If composer autoload missing, fall back to manual requires used by index.php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/PriceCalculatorService.php';
?>
