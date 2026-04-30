<?php
$config = include 'application/config/config.php';
$dbConfig = $config['database'];
try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $stmt = $pdo->query("SELECT * FROM vaults");
    $vaults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($vaults, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
