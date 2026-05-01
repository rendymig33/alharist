<?php
$basePath = __DIR__;
$config = require $basePath . '/application/config/config.php';
require $basePath . '/application/core/Database.php';
require $basePath . '/application/core/Model.php';
require $basePath . '/application/models/Dashboard_model.php';

$model = new Dashboard_model($basePath, $config);

$summary = $model->summary();
echo "Vault balance: " . $summary['vault'] . "\n";
?>