<?php
require_once 'system/init.php';

$basePath = __DIR__;
$config = require $basePath . '/application/config/config.php';
require_once $basePath . '/application/core/Model.php';
require_once $basePath . '/application/models/Pelanggan_model.php';

$model = new Pelanggan_model($basePath, $config);

try {
    $data = $model->search('');
    echo "Customers count: " . count($data) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

try {
    $next = $model->nextCode();
    echo "Next code: " . $next . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
