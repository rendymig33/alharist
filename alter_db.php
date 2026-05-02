<?php
require_once 'system/init.php';

$config = require 'application/config/config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $db->exec("ALTER TABLE customers DROP COLUMN phone;");
    echo "Dropped phone column.\n";
} catch (Exception $e) {
    echo "Phone column might not exist or err: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE customers DROP COLUMN address;");
    echo "Dropped address column.\n";
} catch (Exception $e) {
    echo "Address column might not exist or err: " . $e->getMessage() . "\n";
}

echo "Done.";
