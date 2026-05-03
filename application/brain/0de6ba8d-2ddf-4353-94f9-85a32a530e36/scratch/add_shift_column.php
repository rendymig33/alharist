<?php
$db = new PDO('sqlite:database.db');
try {
    $db->exec("ALTER TABLE sales ADD COLUMN shift INTEGER DEFAULT 1");
    echo "Column 'shift' added successfully.";
} catch (Exception $e) {
    echo "Error or column already exists: " . $e->getMessage();
}
