<?php
// Debug script - akses via: http://localhost/alharist/debug_vault.php
require_once 'application/helpers/functions.php';

// Simulasikan POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST = [
    'action' => 'save_transaction',
    'active_vault_id' => '1',
    'transaction_type' => 'dana_masuk',
    'amount' => '100000',
    'notes' => 'DEBUG TEST',
    'transaction_date' => '2026-05-04',
    'source_vault_id' => '0',
    'target_vault_id' => '0',
];

// Load DB
require_once 'application/config/database.php';
$dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
$pdo = new PDO($dsn, $db['username'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Test amount parsing
$amountRaw = $_POST['amount'];
$amount = (float) str_replace(['.', ','], ['', '.'], $amountRaw);
echo "Amount raw: $amountRaw\n";
echo "Amount parsed: $amount\n";

// Test get vault ID=1
$stmt = $pdo->prepare("SELECT * FROM vaults WHERE id = 1");
$stmt->execute();
$vault = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Vault found: " . ($vault ? $vault['bank_name'] . " balance=" . $vault['balance'] : "NOT FOUND") . "\n";

// List all vaults
$stmt = $pdo->query("SELECT id, bank_name, balance FROM vaults");
$vaults = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nAll vaults:\n";
foreach ($vaults as $v) {
    echo "  ID={$v['id']}, Name={$v['bank_name']}, Balance={$v['balance']}\n";
}
?>
