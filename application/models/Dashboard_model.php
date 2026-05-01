<?php
declare(strict_types=1);

class Dashboard_model extends Model
{
    private function modalVaultBalance(): float
    {
        $exactStatement = $this->db->prepare("
            SELECT COUNT(*) AS total_rows, COALESCE(SUM(balance), 0) AS total_balance
            FROM vaults
            WHERE UPPER(COALESCE(bank_name, '')) = :warung_modal
        ");
        $exactStatement->execute([
            'warung_modal' => 'WARUNG MODAL',
        ]);
        $exactRow = $exactStatement->fetch(PDO::FETCH_ASSOC) ?: ['total_rows' => 0, 'total_balance' => 0];
        if ((int) ($exactRow['total_rows'] ?? 0) > 0) {
            return (float) ($exactRow['total_balance'] ?? 0);
        }

        $keywordStatement = $this->db->prepare("
            SELECT COALESCE(SUM(balance), 0)
            FROM vaults
            WHERE UPPER(COALESCE(bank_name, '')) LIKE :keyword
        ");
        $keywordStatement->execute([
            'keyword' => '%MODAL%',
        ]);

        return (float) $keywordStatement->fetchColumn();
    }

    private function currentProfitSql(): string
    {
        return "
            SELECT COALESCE(SUM(
                sale_items.line_total - (
                    sale_items.qty * (
                        CASE
                            WHEN COALESCE(items.small_unit_qty, 0) > 0 THEN COALESCE(items.purchase_price, 0) / items.small_unit_qty
                            WHEN COALESCE(items.purchase_price, 0) > 0 THEN COALESCE(items.purchase_price, 0)
                            ELSE COALESCE(sale_items.purchase_price, 0)
                        END
                    )
                )
            ), 0)
            FROM sale_items
            INNER JOIN sales ON sales.id = sale_items.sale_id
            LEFT JOIN items ON items.id = sale_items.item_id
            WHERE sales.transaction_date = :transaction_date
        ";
    }

    public function summary(?string $dateFrom = null, ?string $dateTo = null, ?int $vaultId = null): array
    {
        $params = [];
        $salesSql = "SELECT COALESCE(SUM(subtotal), 0) FROM sales";
        $profitSql = $this->currentProfitSql();

        $conditions = [];
        if (!empty($dateFrom)) {
            $conditions[] = "transaction_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        if (!empty($dateTo)) {
            $conditions[] = "transaction_date <= :date_to";
            $params['date_to'] = $dateTo;
        }

        if (empty($dateFrom) && empty($dateTo)) {
            $today = date('Y-m-d');
            $conditions[] = "transaction_date = :today";
            $params['today'] = $today;
        }

        if ($vaultId > 0) {
            $salesSql .= " WHERE " . implode(' AND ', $conditions) . " AND vault_id = :vault_id";
            // For profit, we need to handle the join in currentProfitSql
            $profitSql = str_replace('WHERE sales.transaction_date = :transaction_date', "WHERE " . implode(' AND ', str_replace('transaction_date', 'sales.transaction_date', $conditions)) . " AND sales.vault_id = :vault_id", $profitSql);
            $params['vault_id'] = $vaultId;
        } else {
            $salesSql .= " WHERE " . implode(' AND ', $conditions);
            $profitSql = str_replace('WHERE sales.transaction_date = :transaction_date', "WHERE " . implode(' AND ', str_replace('transaction_date', 'sales.transaction_date', $conditions)), $profitSql);
        }

        $salesStmt = $this->db->prepare($salesSql);
        $salesStmt->execute($params);
        $sales = (float) $salesStmt->fetchColumn();

        // Profit params might need adjustment if we changed the keys
        $profitStmt = $this->db->prepare($profitSql);
        $profitStmt->execute($params);
        $profit = (float) $profitStmt->fetchColumn();

        $debts = (float) $this->db->query("SELECT COALESCE(SUM(total_debt - paid_amount), 0) FROM debts WHERE status = 'Belum Lunas'")->fetchColumn();
        $stock = (int) $this->db->query("SELECT COALESCE(SUM(stock), 0) FROM items")->fetchColumn();

        if ($vaultId > 0) {
            $vaultStatement = $this->db->prepare("SELECT COALESCE(balance, 0) FROM vaults WHERE id = :vault_id LIMIT 1");
            $vaultStatement->execute([
                'vault_id' => $vaultId,
            ]);
            $vault = (float) $vaultStatement->fetchColumn();
        } else {
            $vault = $this->modalVaultBalance();
        }

        return [
            'sales' => $sales,
            'profit' => $profit,
            'net_profit' => $profit - (float) $this->config['daily_capital'],
            'debts' => $debts,
            'stock' => $stock,
            'vault' => $vault,
        ];
    }

    public function lowStockItems(): array
    {
        $statement = $this->db->query("SELECT * FROM items WHERE stock <= 5 ORDER BY stock ASC, name ASC LIMIT 8");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function expiringItems(): array
    {
        $date = date('Y-m-d', strtotime('+30 days'));
        $statement = $this->db->prepare("SELECT * FROM items WHERE exp_date IS NOT NULL AND exp_date <= :date ORDER BY exp_date ASC LIMIT 8");
        $statement->execute(['date' => $date]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
