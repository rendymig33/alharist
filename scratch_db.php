<?php
require 'application/core/Config.php';
require 'application/core/Database.php';
require 'application/core/Model.php';
require 'application/models/Keuangan_model.php';

// Mocking some stuff if needed, but let's see if we can just connect.
// Actually, it's easier to just check the database.php if it exists.
$db = null;
if (file_exists('application/config/database.php')) {
    include 'application/config/database.php';
}
if (isset($db)) {
    print_r($db);
} else {
    echo 'Database configuration not available';
}
