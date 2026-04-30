<?php
return [
    'app_name' => 'Kasir Desktop CI3',
    'daily_capital' => 200000,
    'currency' => 'Rp',
    'default_timezone' => 'Asia/Jakarta',
    'database' => [
        'driver' => 'mysql',
        'sqlite_path' => __DIR__ . '/../../storage/kasir.sqlite',
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'alharist',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
