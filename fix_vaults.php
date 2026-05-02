<?php
require_once 'system/init.php';

$config = require 'application/config/config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$salesStmt = $db->query("SELECT * FROM sales WHERE payment_type IN ('Tunai', 'QRIS')");
$sales = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

$vaultDeductions = [];

foreach ($sales as $sale) {
    $saleId = (int)$sale['id'];
    $itemsStmt = $db->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
    $itemsStmt->execute([$saleId]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    $vaultTotals = [];
    $vaultCosts = [];

    foreach ($items as $item) {
        $vaultId = (int) ($item['vault_id'] ?? 0);
        if ($vaultId > 0) {
            $cost = (float)$item['line_total'] - (float)$item['line_profit'];
            $vaultCosts[$vaultId] = ($vaultCosts[$vaultId] ?? 0) + $cost;
            $vaultTotals[$vaultId] = ($vaultTotals[$vaultId] ?? 0) + (float)$item['line_total'];
        }
    }

    if (empty($vaultTotals) && (int)$sale['vault_id'] > 0) {
        $vaultId = (int)$sale['vault_id'];
        $cost = (float)$sale['subtotal'] - (float)$sale['total_profit'];
        $vaultCosts[$vaultId] = ($vaultCosts[$vaultId] ?? 0) + $cost;
    }

    foreach ($vaultCosts as $vid => $costToDeduct) {
        $vaultDeductions[$vid] = ($vaultDeductions[$vid] ?? 0) + $costToDeduct;
    }
}

echo "Deductions to apply:\n";
print_r($vaultDeductions);

foreach ($vaultDeductions as $vid => $costToDeduct) {
    if ($costToDeduct > 0) {
        $updateStmt = $db->prepare("UPDATE vaults SET balance = balance - ? WHERE id = ?");
        $updateStmt->execute([$costToDeduct, $vid]);
        echo "Vault $vid deducted by $costToDeduct\n";
    }
}

echo "Done.";
