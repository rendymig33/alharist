<?php
declare(strict_types=1);

if (!is_dir(__DIR__ . '/storage/sessions')) {
    mkdir(__DIR__ . '/storage/sessions', 0777, true);
}

session_save_path(__DIR__ . '/storage/sessions');
session_start();

$basePath = __DIR__;

require $basePath . '/application/core/App.php';
require $basePath . '/application/core/Controller.php';
require $basePath . '/application/core/Model.php';
require $basePath . '/application/helpers/app_helper.php';

$app = new App($basePath);
$app->run();
